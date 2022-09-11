<?php

/**
 * JobeetCategoryAffiliate form.
 *
 * @package    jobeetdocs
 * @subpackage form
 * @author     Your name here
 */
class JobeetCategoryAffiliateForm extends BaseJobeetCategoryAffiliateForm
{
  public function configure()
  {
    unset($this['jobeet_category_affiliate_list']);
  }
}
