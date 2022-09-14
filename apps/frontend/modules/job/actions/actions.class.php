<?php

class jobActions extends sfActions
{
  public function executeSearch(sfWebRequest $request)
  {
    $this->forwardUnless($query = $request->getParameter('query'), 'job', 'index');

    $this->jobs = JobeetJobPeer::getForLuceneQuery($query);
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->categories = JobeetCategoryPeer::getWithJobs();
    $this->getWithJobs = [''];
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->job = $this->getRoute()->getObject();
  }

  public function executeNew(sfWebRequest $request)
  {
    $job = new JobeetJob();
    $job->setType('part-time');

    $this->form = new JobeetJobForm($job);
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new JobeetJobForm();
    $this->processForm($request, $this->form);
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->form = new JobeetJobForm($this->getRoute()->getObject());
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->form = new JobeetJobForm($this->getRoute()->getObject());
    $this->processForm($request, $this->form);
    $this->setTemplate('new');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $job = $this->getRoute()->getObject();
    $job->delete();

    $this->redirect('job/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind(
      $request->getParameter($form->getName()),
      $request->getFiles($form->getName())
    );

    if ($form->isValid())
    {
      $job = $form->save();

      $this->redirect('job_show', $job);
    }
  }

  public function executePublish(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $job = $this->getRoute()->getObject();
    $job->publish();

    $this->getUser()->setFlash('notice', sprintf('Your job is now online for %s days.', sfConfig::get('app_active_days')));

    $this->redirect('job_show_user', $job);
  }
}
