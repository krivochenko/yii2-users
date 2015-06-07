<?php

namespace budyaga\users\rbac;

use yii\rbac\Rule;
use Yii;

/**
 * Checks if authorID matches user passed via params
 */
class NoElderRankRule extends Rule
{
    public $name = 'noElderRank';

    const DEFAULT_RANK_VALUE = 10;

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
		if (!isset($params['user'])) {
			return false;
		}
		$currentUserRank = false;
		$targetUserRank = false;
		
		$ranks = [
			'administrator' => 100,
			'moderator' => 90
		];

        $auth = Yii::$app->authManager;

        foreach ($ranks as $key => $value) {
            if (!$currentUserRank && Yii::$app->user->can($key)) {
                $currentUserRank = $value;
            }

            if (!$targetUserRank && $auth->checkAccess($params['user']->id, $key)) {
                $targetUserRank = $value;
            }

            if ($currentUserRank && $targetUserRank) {
                break;
            }
        }

        if (!$targetUserRank) {
            $targetUserRank = self::DEFAULT_RANK_VALUE;
        }
		
        return $currentUserRank && $currentUserRank>=$targetUserRank;
    }
}