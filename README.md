
# Sculpin Update Bundle

The bundle implements an `update` command which rebuilds any source files
that have been updated since the last build.

## Warning

This bundle uses some hackish method to achieve the functionality. Future
versions of Sculpin MAY break this package completely. There are no
guarentee of any sort for the bundle. You have been warned.

## Installation

The installation procedure is the same as other Sculpin bundles. Create a
custom Sculpin kernel and register this bundle in the
`getAdditionalSculpinBundles` method.

```php

use Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel;
use Kmchan\Sculpin\UpdateBundle\SculpinUpdateBundle;

class SculpinKernel extends AbstractKernel
{
	protected function getAdditionalSculpinBundles(): array
	{
		return [
			// some sculpin bundles...
			SculpinUpdateBundle::class,
			// more sculpin bundles...
		];
	}
}

```

## Usage

The bundle adds a new `update` command. The command works like `generate`,
except that only files modified since the last build time will be processed.

After installing the bundle, both `generate` and `update` commands will
generate a marker file in the output directory. Its mtime indicates the last
build time. Subsequent update commands will use the marker file to retrieve
the last build time and filter out files that have not been changed since
that time.

## Configuration

The path to the marker file, relative to the output directory, can be
configured in `sculpin_kernel.yml`. The configuration key is
`update.marker.file.path`, and its default value is `.build_timestamp`.

Example:

```yaml

sculpin_update:
    marker:
        file:
            path: ".build"

```

