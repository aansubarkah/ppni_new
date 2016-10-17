<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Senders Model
 *
 * @property \Cake\ORM\Association\HasMany $Letters
 *
 * @method \App\Model\Entity\Sender get($primaryKey, $options = [])
 * @method \App\Model\Entity\Sender newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Sender[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Sender|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sender patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Sender[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Sender findOrCreate($search, callable $callback = null)
 */
class SendersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('senders');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->hasMany('Letters', [
            'foreignKey' => 'sender_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->boolean('active')
            ->requirePresence('active', 'create')
            ->notEmpty('active');

        return $validator;
    }
}
