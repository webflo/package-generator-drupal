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
    $drupal_drupal_repository = $this->gitObject->getRepository();
    $scaffold_assets_repository = $this->metapackage_repository;
    $drupal_drupal_path = $drupal_drupal_repository->getPath();

    $scaffold_file_mapping = $this->config['composer']['file-mapping'];

    // The file mappings are structured as follows:
    //   "[web-root]/sites/example.settings.local.php": "assets/example.settings.local.php"
    // The key is the path to the asset in the Drupal project, and the value
    // is the path to the asset in the Scaffold Assets.
    // The Drupal project path is where we read the asset from now, as we are
    // building the Scaffold Assets. It is also where the asset will ultimately
    // be installed to when the Drupal Scaffold operation actually executes.
    // The scaffold assets path is the location where we will write the asset
    // to as we build the Scaffold Assets project.
    foreach ($scaffold_file_mapping as $drupal_project_asset_path => $scaffold_asset_path) {
      if (is_array($scaffold_asset_path)) {
        if (isset($scaffold_asset_path['shadow'])) {
          unset($scaffold_file_mapping[$drupal_project_asset_path]['shadow']);
          $drupal_project_asset_path = $scaffold_asset_path['shadow'];
        }
        $scaffold_asset_path = $scaffold_asset_path['path'];
      }
      $source_path = str_replace('[web-root]', $drupal_drupal_path, $drupal_project_asset_path);
      $target_path = $scaffold_assets_repository->getPath() . '/' . $scaffold_asset_path;
      // If the source scaffold file does not exist in this revision, then
      // remove it from the file mapping path.
      if (!file_exists($source_path)) {
        unset($scaffold_file_mapping[$drupal_project_asset_path]);
      }
      else {
        $fs->mkdir(dirname($target_path));
        copy($source_path, $target_path);
        $scaffold_assets_repository->run('add', [$scaffold_asset_path]);
      }
    }

    return $scaffold_file_mapping;
  }

}
