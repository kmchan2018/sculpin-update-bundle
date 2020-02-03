
# Design

This docuemnt explains the design of this bundle.

## Introduction

Sculpin supports incremental build through the use of watch option. This
option will cause Sculpin to watch for updated files and rebuild them. It
requires a Sculpin process to run at all time and therefore is not suitable
for CI/CD pipeline.

Outside of the flag, however, Sculpin simply do not support incremental build.
Each invocation of `generate` will rebuild the site from scratch.

This bundle is designed to enable incremental build outside of the watch
option.

## How Sculpin Finds Source Files and Detect Changes

Sculpin locates source files using data source classes. These classes all
implements the `Sculpin\Core\Source\DataSourceInterface` interface. Among
all data source classes, both `FilesystemDataSource` and
`ConfigFilesystemDataSource` classes are the real workhorse that perform
the actual discovery.

The `FilesystemDataSource` class is responsible for scanning the source
directory for files, filtering them according to modification time and
merging eligible files to a source set. Modification time filtering
is handled by a private `sinceTime` property that indicates the minimum
modification time a file should have for it to be eligible. The property
is initialized to UNIX epoch for the initial refresh call, and each
refresh call will update the property to the invocation time. Effectively,
the class will merge all source files into the source set in the initial
refresh call, but only updated source files in subsequent refresh calls.
All files merged by the class are flagged as changed.

The `ConfigFilesystemDataSource` class is responsbile for checking
configuration files for updates. It uses a similar method to handle
modification time. However, instead of merging new sources into the
source set, it merely flags existing files in the source set as
changed when update is detected.

## How to Enable Incremental Build

The reason why Sculpin does not support incremental build without the
watch option is due to both classes merging all files as changed during
the initial refresh call.

To enable incremental build, we need to devise a method to make both
`FilesystemDataSource` and `ConfigFilesystemDataSource` to properly flag
the files in the initial refresh call.

For `ConfigFilesystemDataSource`, updating the `sinceTime` property is
sufficient to meet the goal. The only issue is that the class is declared
final, the property is declared private, and no accessor is provided to
update the property, necessitating the use of reflection.

For `FilesystemDataSource`, updating the `sinceTime` property alone is
not sufficient. Doing so will cause unchanged files to be skipped in the
initial refresh. However, if the class is updated to perform 2 scans in
the initial refresh, one with default `sinceTime` and one with updated
`sinceTime`, and combine the result, it will do the trick.

## Bundle Class Hierarchy

The classes in the bundle is structured as follow:

Update time marker classes are responsible for persistent storage of last
build time. They are declared under the `Kmchan\Sculpin\UpdateBundle\Marker`
namespace, and implements the `MarkerInterface` interface under that
namespace.

Update time propagator classes are responsible for propagating the last build
time from marker to the data source and enabling incremental build. They are
declared under the `Kmchan\Sculpin\UpdateBundle\Propagator` namespace, and
implements the `PropagatorInterface` interface under that namespace.

Class `Kmchan\Sculpin\UpdateBundle\Source\ReplacementFilesystemDataSource`
wraps around the original filesystem data source class and implement the logic
required to support incremental build.

Class `Kmchan\Sculpin\UpdateBundle\Event\MarkerWriter` implements an event
subscriber to write the build time to marker after each run.

Class `Kmchan\Sculpin\UpdateBundle\Command\UpdateCommand` implements a
command that can be invoked by the users to build the site incrementally. The
class is a slight adaptation of the default generate command. It precedes the
actual generation code by a call to propagator to enable incremental build.

Class `Kmchan\Sculpin\UpdateBundle\DependencyInjection\Compiler\MarkerPass`
implements a container compiler pass to process any services with
`sculpin_update.marker` tag.

Class `Kmchan\Sculpin\UpdateBundle\DependencyInjection\Compiler\PropagatorPass`
implements a container compiler pass to process any services with
`sculpin_update.propagator` tag. It also automatically creates a corresponding
propagator service for each data source service with `sculpin.data_source`
tag.

## Bundle Services

The bundle registers a few additional services in the container.

Service `sculpin_update.marker` is a `CompositeMarker` instance that contains
all markers for the site. By default, it contains a single `FileMarker` with
id `sculpin_update.marker.file`. Moreover, any services with the tag
`sculpin_update.marker` will be added to the instance as well.

Service `sculpin_update.propagator` is a `CompositePropagator` instance that
contains all propagators for the data sources. The bundle will generate a
propagator for each `FilesystemDataSource` and `ConfigFilesystemDataSource`
instance with tag `sculpin.data_source`. Moreover, any services with the tag
`sculpin_update.propagator` will be added to the instance as well.

Service `sculpin_update.writer` is a `MarkerWriter` instance that writes the
building time to update time marker after each run.

Service `sculpin_update.command` is a `UpdateCommand` instance and expose an
`update` command for users to build the site incrementally.

