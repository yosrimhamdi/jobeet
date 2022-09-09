<?php

class JobeetCategory extends BaseJobeetCategory {
  public function __toString()
  {
    return $this->getName();
  }

  public function getActiveJobs($max = null)
  {
    $criteria = new Criteria();
    $criteria->add(JobeetJobPeer::CATEGORY_ID, $this->getId());

    if ($max) {
      $criteria->setLimit($max);
    }

    return JobeetJobPeer::getActiveJobs($criteria);
  }

  public function countActiveJobs()
  {
    $criteria = new Criteria();
    $criteria->add(JobeetJobPeer::CATEGORY_ID, $this->getId());

    return JobeetJobPeer::countActiveJobs($criteria);
  }

  public function setName($name)
  {
    parent::setName($name);

    $this->setSlug(Jobeet::slugify($name));
  }
}
