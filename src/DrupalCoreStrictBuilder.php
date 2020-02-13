<?php

namespace PackageGeneratorDrupal;

class DrupalCoreStrictBuilder extends DrupalPackageBuilder {

  protected $defaultMetadata = [
    'name' => 'webflo/drupal-core-strict',
    'type' => 'metapackage',
    'description' => 'Locked core dependencies',
    'license' => 'GPL-2.0-or-later',
  ];

  public function getPackage() {
    $composer = $this->defaultMetadata;

    if (isset($this->composerLock['packages'])) {
      foreach ($this->composerLock['packages'] as $package) {
        $name = strtolower($package['name']);
        $composer['require'][$name] = $this->packageToVersion($package);
      }
    }

    if (isset($this->composerLock['packages-dev'])) {
      foreach ($this->composerLock['packages-dev'] as $package) {
        $name = strtolower($package['name']);
        $composer['require-dev'][$name] = $this->packageToVersion($package);
      }
    }

    return $composer;
  }

}
