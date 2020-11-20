<?php

namespace PackageGeneratorDrupal;

class DrupalCoreStrictBuilder extends DrupalPackageBuilder {

  public function getPackage() {
    $composer =  $this->config['composer']['metadata'];

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
