<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * EvidencesLetters Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Evidences
 * @property \Cake\ORM\Association\BelongsTo $Letters
 *
 * @method \App\Model\Entity\EvidencesLetter get($primaryKey, $options = [])
 * @method \App\Model\Entity\EvidencesLetter newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\EvidencesLetter[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\EvidencesLetter|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EvidencesLetter patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\EvidencesLetter[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\EvidencesLetter findOrCreate($search, callable $callback = null)
 */
class EvidencesLettersTable extends Table
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

        $this->table('evidences_letters');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Evidences', [
            'foreignKey' => 'evidence_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Letters', [
            'foreignKey' => 'letter_id',
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
        $rules->add($rules->existsIn(['letter_id'], 'Letters'));

        return $rules;
    }
}
