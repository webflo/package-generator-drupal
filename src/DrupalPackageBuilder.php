<?php

namespace PackageGeneratorDrupal;

use Gitonomy\Git\Reference;
use Gitonomy\Git\Reference\Branch;
use Gitonomy\Git\Reference\Tag;
use PackageGenerator\BuilderInterface;

abstract class DrupalPackageBuilder implements BuilderInterface {

  /**
   * @var string
   */
  protected $referenceName;

  /**
   * @var array
   */
  protected $composerJson;

  /**
   * @var array
   */
  protected $composerLock;

  /**
   * @var Reference
   */
  protected $gitObject;

  public function __construct(array $composerJson, array $composerLock, Reference $gitObject) {
    $this->composerJson = $composerJson;
    $this->composerLock = $composerLock;
    $this->gitObject = $gitObject;

    if ($this->gitObject instanceof Branch) {
      $this->referenceName = str_replace('origin/', '', $this->gitObject->getName());
    }
    elseif ($this->gitObject instanceof Tag) {
      $this->referenceName = $this->gitObject->getName();
    }
    else {
      throw new \LogicException("gitObject should be Branch or Tag");
    }
  }

  public function getCommitMessage() {
    $msg[] = "Update composer.json ({$this->referenceName})";
    $msg[] = '';
    $msg[] = 'Reference: http://cgit.drupalcode.org/drupal/commit/?id=' . $this->gitObject->getCommitHash();

    return implode("\n", $msg);
  }

  public function packageToVersion(array $package) {
    if (substr($package['version'], 0, 4) == 'dev-') {
      return $package['version'] . '#' . $package['source']['reference'];
    }
    return $package['version'];
  }

}
