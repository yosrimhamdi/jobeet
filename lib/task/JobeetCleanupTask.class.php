<?php

class JobeetCleanupTask {
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    // cleanup Lucene index
    $index = JobeetJobPeer::getLuceneIndex();

    $criteria = new Criteria();
    $criteria->add(JobeetJobPeer::EXPIRES_AT, time(), Criteria::LESS_THAN);
    $jobs = JobeetJobPeer::doSelect($criteria);
    foreach ($jobs as $job)
    {
      if ($hit = $index->find('pk:'.$job->getId()))
      {
        $index->delete($hit->id);
      }
    }

    $index->optimize();

    $this->logSection('lucene', 'Cleaned up and optimized the job index');

    // Remove stale jobs
    $nb = JobeetJobPeer::cleanup($options['days']);

    $this->logSection('propel', sprintf('Removed %d stale jobs', $nb));
  }
}