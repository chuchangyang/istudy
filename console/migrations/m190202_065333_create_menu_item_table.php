<?php

use console\db\Migration;

/**
 * Handles the creation of table `$this->table`.
 */
class m190202_065333_create_menu_item_table extends Migration
{
    public $table = '{{%menu_item}}';

    public $tableOption = 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';

    public $menuTable = '{{%menu}}';

    public $menuFk = 'FK_MENU_ITEM_MENU_ID_MENU_ID';

    public $parentFk = 'FK_MENU_ITEM_PARENT_ID_MENU_ITEM_ID';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey()->unsigned(),
            'parent_id' => $this->integer(11)->unsigned(),
            'menu_id' => $this->integer(11)->unsigned()->notNull(),
            'title' => $this->string()->notNull(),
            'url'   => $this->string()->notNull()->defaultValue('#'),
            'position' => $this->integer()->unsigned()->notNull(),
        ], $this->tableOption);


        $this->addForeignKey($this->menuFk, 
            $this->table, 
            'menu_id',
            $this->menuTable,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey($this->parentFk,
            $this->table,
            'parent_id',
            $this->table,
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
    

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey($this->menuFk, $this->table);
        $this->dropForeignKey($this->parentFk, $this->table);
        $this->dropTable($this->table);
    }
}