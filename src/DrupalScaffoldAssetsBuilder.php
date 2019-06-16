<?php

namespace PackageGeneratorDrupal;

use Symfony\Component\Filesystem\Filesystem;

class DrupalScaffoldAssetsBuilder extends DrupalPackageBuilder {

  public function getPackage() {
    $composer = $this->config['composer']['metadata'];

    $composer['extra']['composer-scaffold']['file-mapping'] = $this->addFileMapping();

    return $composer;
  }

  protected function addFileMapping() {
    $fs = new Filesystem();
    $source_repository = $this->gitObject->getRepository();
    $root_path = $source_repository->getPath();

    $scaffold_file_mapping = $this->config['composer']['file-mapping'];

    foreach ($scaffold_file_mapping as $asset_path => $metapackage_path) {
      $source_path = str_replace('[web-root]', $root_path, $asset_path);
      $target_path = $this->metapackage_repository->getPath() . '/' . $metapackage_path;
      // If the source scaffold file does not exist in this revision, then
      // remove it from the file mapping path.
      if (!file_exists($source_path)) {
        unset($scaffold_file_mapping[$asset_path]);
      }
      else {
        $fs->mkdir(dirname($target_path));
        copy($source_path, $target_path);
        $this->metapackage_repository->run('add', [$metapackage_path]);
      }
    }

    return $scaffold_file_mapping;
  }

}
