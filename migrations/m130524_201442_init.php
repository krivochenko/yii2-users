<?php

use yii\db\Schema;
use yii\db\Migration;
use budyaga\users\models\User;
use yii\rbac\Item;
use budyaga\users\Module;

class m130524_201442_init extends Migration
{
    public function up()
    {
        Module::registerTranslations();

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        //таблица user
        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'email' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'photo' => Schema::TYPE_STRING.' NULL DEFAULT NULL',
            'sex' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT ' . User::SEX_MALE,
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        //таблица user_password_reset_token
        $this->createTable('{{%user_password_reset_token}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'token' => Schema::TYPE_STRING.' NULL DEFAULT NULL',
        ], $tableOptions);
        $this->addForeignKey('user_password_reset_token_user_id_fk', '{{%user_password_reset_token}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

        //таблица user_change_email_token
        $this->createTable('{{%user_email_confirm_token}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'old_email' => Schema::TYPE_STRING,
            'old_email_token' => Schema::TYPE_STRING,
            'old_email_confirm' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',
            'new_email' => Schema::TYPE_STRING . ' NOT NULL',
            'new_email_token' => Schema::TYPE_STRING . ' NOT NULL',
            'new_email_confirm' => Schema::TYPE_SMALLINT . ' NULL DEFAULT 0',
        ], $tableOptions);
        $this->addForeignKey('user_email_confirm_token_user_id_fk', '{{%user_email_confirm_token}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

        //таблица user_oauth_key
        $this->createTable('{{%user_oauth_key}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER,
            'provider_id' => Schema::TYPE_INTEGER,
            'provider_user_id' => Schema::TYPE_STRING.'(255)'
        ], $tableOptions);
        $this->addForeignKey('user_oauth_key_user_id_fk', '{{%user_oauth_key}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

        //таблица auth_rule
        $this->createTable('{{%auth_rule}}', [
            'name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER

        ], $tableOptions);
        $this->addPrimaryKey('auth_rule_pk', '{{%auth_rule}}', 'name');

        //таблица auth_item
        $this->createTable('{{%auth_item}}', [
            'name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'type' => Schema::TYPE_INTEGER.' NOT NULL',
            'description' => Schema::TYPE_TEXT,
            'rule_name' => Schema::TYPE_STRING.'(64)',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER
        ], $tableOptions);
        $this->addPrimaryKey('auth_item_name_pk', '{{%auth_item}}', 'name');
        $this->addForeignKey('auth_item_rule_name_fk', '{{%auth_item}}', 'rule_name', '{{%auth_rule}}',  'name', 'SET NULL', 'CASCADE');
        $this->createIndex('auth_item_type_index', '{{%auth_item}}', 'type');

        //таблица auth_item_child
        $this->createTable('{{%auth_item_child}}', [
            'parent' => Schema::TYPE_STRING.'(64) NOT NULL',
            'child' => Schema::TYPE_STRING.'(64) NOT NULL'
        ], $tableOptions);
        $this->addPrimaryKey('auth_item_child_pk', '{{%auth_item_child}}', array('parent', 'child'));
        $this->addForeignKey('auth_item_child_parent_fk', '{{%auth_item_child}}', 'parent', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('auth_item_child_child_fk', '{{%auth_item_child}}', 'child', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');

        //таблица auth_assignment
        $this->createTable('{{%auth_assignment}}', [
            'item_name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER.'(11) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER
        ], $tableOptions);
        $this->addPrimaryKey('auth_assignment_pk', '{{%auth_assignment}}', array('item_name', 'user_id'));
        $this->addForeignKey('auth_assignment_item_name_fk', '{{%auth_assignment}}', 'item_name', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('auth_assignment_user_id_fk', '{{%auth_assignment}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

        //аккаунт для администратора и права
        $this->batchInsert('{{%user}}', ['username', 'auth_key', 'password_hash', 'email', 'status', 'created_at', 'updated_at'], [
            [
                Yii::t('users', 'MIGRATION_ADMINISTRATOR'),
                Yii::$app->security->generateRandomString(),
                Yii::$app->security->generatePasswordHash('administrator@example.com'),
                'administrator@example.com',
                User::STATUS_ACTIVE,
                time(),
                time()
            ],
            [
                Yii::t('users', 'MIGRATION_MODERATOR'),
                Yii::$app->security->generateRandomString(),
                Yii::$app->security->generatePasswordHash('moderator@example.com'),
                'moderator@example.com',
                User::STATUS_ACTIVE,
                time(),
                time()
            ]
        ]);
        $this->insert('{{%auth_rule}}', [
            'name' => 'noElderRank',
            'data' => 'O:34:"budyaga\users\rbac\NoElderRankRule":3:{s:4:"name";s:11:"noElderRank";s:9:"createdAt";N;s:9:"updatedAt";i:1431880756;}',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'rule_name', 'created_at', 'updated_at'], [
            ['administrator', Item::TYPE_ROLE, Yii::t('users', 'MIGRATION_ADMINISTRATOR'), NULL, time(), time()],
            ['moderator', Item::TYPE_ROLE, Yii::t('users', 'MIGRATION_MODERATOR'), NULL, time(), time()],
            ['rbacManage', Item::TYPE_PERMISSION, Yii::t('users', 'MIGRATION_RBAC_MANAGE'), NULL, time(), time()],
            ['userCreate', Item::TYPE_PERMISSION, Yii::t('users', 'MIGRATION_USER_CREATE'), NULL, time(), time()],
            ['userDelete', Item::TYPE_PERMISSION, Yii::t('users', 'MIGRATION_USER_DELETE'), NULL, time(), time()],
            ['userManage', Item::TYPE_PERMISSION, Yii::t('users', 'MIGRATION_USER_MANAGE'), NULL, time(), time()],
            ['userPermissions', Item::TYPE_PERMISSION, Yii::t('users', 'MIGRATION_USER_PERMISSIONS'), NULL, time(), time()],
            ['userUpdate', Item::TYPE_PERMISSION, Yii::t('users', 'MIGRATION_USER_UPDATE'), NULL, time(), time()],
            ['userUpdateNoElderRank', Item::TYPE_PERMISSION, Yii::t('users', 'MIGRATION_USER_UPDATE_NO_ELDER_RANK'), 'noElderRank', time(), time()],
            ['userView', Item::TYPE_PERMISSION, Yii::t('users', 'MIGRATION_USER_VIEW'), NULL, time(), time()],
        ]);
        $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'], [
            ['administrator', 'rbacManage'],
            ['administrator', 'userCreate'],
            ['administrator', 'userDelete'],
            ['administrator', 'userPermissions'],
            ['administrator', 'userUpdate'],
            ['administrator', 'moderator'],
            ['moderator', 'userManage'],
            ['moderator', 'userView'],
            ['moderator', 'userUpdateNoElderRank'],
            ['userUpdateNoElderRank', 'userUpdate'],
        ]);
        $this->batchInsert('{{%auth_assignment}}', ['item_name', 'user_id', 'created_at', 'updated_at'], [
            ['administrator', 1, time(), time()],
            ['moderator', 2, time(), time()],
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%auth_assignment}}');
        $this->dropTable('{{%auth_item_child}}');
        $this->dropTable('{{%auth_item}}');
        $this->dropTable('{{%auth_rule}}');
        $this->dropTable('{{%user_oauth_key}}');
        $this->dropTable('{{%user_email_confirm_token}}');
        $this->dropTable('{{%user_password_reset_token}}');
        $this->dropTable('{{%user}}');
    }
}
