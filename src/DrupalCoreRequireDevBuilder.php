<?php

namespace PackageGeneratorDrupal;

class DrupalCoreRequireDevBuilder extends DrupalPackageBuilder
{

  protected $defaultMetadata = [
    'name' => 'webflo/drupal-core-require-dev',
    'type' => 'metapackage',
    'description' => 'require-dev dependencies from drupal/core',
    'license' => 'GPL-2.0-or-later',
  ];

  public function getPackage()
  {
    $composer = $this->defaultMetadata;

    // The relevant require-dev constraints are stored in core/composer.json.
    $path = $this->gitObject->getRepository()->getPath() . '/core/composer.json';
    if (file_exists($path)) {
      $composerJsonData = json_decode(file_get_contents($path), TRUE);
      $composer['require'] = isset($composerJsonData['require-dev']) ? $composerJsonData['require-dev'] : [];
    }

    return $composer;
  }

}
