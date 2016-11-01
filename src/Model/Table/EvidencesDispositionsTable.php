<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * EvidencesDispositions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Evidences
 * @property \Cake\ORM\Association\BelongsTo $Dispositions
 *
 * @method \App\Model\Entity\EvidencesDisposition get($primaryKey, $options = [])
 * @method \App\Model\Entity\EvidencesDisposition newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\EvidencesDisposition[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\EvidencesDisposition|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EvidencesDisposition patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\EvidencesDisposition[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\EvidencesDisposition findOrCreate($search, callable $callback = null)
 */
class EvidencesDispositionsTable extends Table
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

        $this->table('evidences_dispositions');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Evidences', [
            'foreignKey' => 'evidence_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Dispositions', [
            'foreignKey' => 'disposition_id',
            'joinType' => 'INNER'
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
            ->boolean('active')
            ->requirePresence('active', 'create')
            ->notEmpty('active');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['evidence_id'], 'Evidences'));
        $rules->add($rules->existsIn(['disposition_id'], 'Dispositions'));

        return $rules;
    }
}
