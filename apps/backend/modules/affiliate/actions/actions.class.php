<?php

require_once dirname(__FILE__).'/../lib/affiliateGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/affiliateGeneratorHelper.class.php';

class affiliateActions extends autoAffiliateActions
{
  public function executeListActivate()
  {
    $this->getRoute()->getObject()->activate();

    $this->redirect('jobeet_affiliate');
  }

  public function executeListDeactivate()
  {
    $this->getRoute()->getObject()->deactivate();

    $this->redirect('jobeet_affiliate');
  }

  public function executeBatchActivate(sfWebRequest $request)
  {
    $affiliates = JobeetAffiliatePeer::retrieveByPks($request->getParameter('ids'));

    foreach ($affiliates as $affiliate)
    {
      $affiliate->activate();

      $this->getMailer()->composeAndSend(
        array('bavary1515@gmail.com' => 'Jobeet Bot'),
        $affiliate->getEmail(),
          'Jobeet affiliate token',
          <<<EOF
  Your Jobeet affiliate account has been activated.
   
  Your token is {$affiliate->getToken()}.
   
  The Jobeet Bot.
EOF
      );
    }

    $this->redirect('jobeet_affiliate');
  }

  public function executeBatchDeactivate(sfWebRequest $request)
  {
    $affiliates = JobeetAffiliatePeer::retrieveByPks($request->getParameter('ids'));

    foreach ($affiliates as $affiliate)
    {
      $affiliate->deactivate();
    }

    $this->redirect('jobeet_affiliate');
  }
}