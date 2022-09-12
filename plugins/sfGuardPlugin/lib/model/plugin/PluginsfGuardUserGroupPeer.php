<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: PluginsfGuardUserGroupPeer.php 7634 2008-02-27 18:01:40Z fabien $
 */
class PluginsfGuardUserGroupPeer extends BasesfGuardUserGroupPeer
{
  /**
   * Adds a new sfGuardUserGroup
   *
   * @param int $userId
   * @param int $groupId
   * @return \sfGuardUserGroup
   * @throws ErrorException
   */
  public static function add($userId, $groupId)
  {
    $sfGuardUser = sfGuardUserPeer::retrieveByPK($userId);
    if ($sfGuardUser == null)
    {
      throw new ErrorException(sprintf('sfGuardUser with %s id not found', $userId));
    }

    $sfGuardGroup = sfGuardGroupPeer::retrieveByPK($groupId);
    if ($sfGuardGroup == null)
    {
      throw new ErrorException(sprintf('sfGuardGroup with %s id not found', $groupId));
    }

    $sfGuardUserGroup = new sfGuardUserGroup();
    $sfGuardUserGroup->setUserId($userId);
    $sfGuardUserGroup->setGroupId($groupId);
    $sfGuardUserGroup->save();

    return $sfGuardUserGroup;
  }
}
