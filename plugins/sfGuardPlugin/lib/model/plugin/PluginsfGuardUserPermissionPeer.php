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
 * @version    SVN: $Id: PluginsfGuardUserPermissionPeer.php 7634 2008-02-27 18:01:40Z fabien $
 */
class PluginsfGuardUserPermissionPeer extends BasesfGuardUserPermissionPeer
{
  /*
   * Adds a new sfGuardUserPermission
   *
   * @param int $userId
   * @param int $permissionId
   * @return \sfGuardUserPermission
   * @throws ErrorException
   */
  public static function add($userId, $permissionId)
  {
    $sfGuardUser = sfGuardUserPeer::retrieveByPK($userId);
    if ($sfGuardUser == null)
    {
      throw new ErrorException(sprintf('sfGuardUser with %s id not found', $userId));
    }

    $sfGuardPermission = sfGuardPermissionPeer::retrieveByPK($permissionId);
    if ($sfGuardPermission == null)
    {
      throw new ErrorException(sprintf('sfGuardPermission with %s id not found', $permissionId));
    }

    $sfGuardUserPermission = new sfGuardUserPermission();
    $sfGuardUserPermission->setUserId($userId);
    $sfGuardUserPermission->setPermissionId($permissionId);
    $sfGuardUserPermission->save();

    return $sfGuardUserPermission;
  }
}
