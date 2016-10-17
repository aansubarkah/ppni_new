<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Departement Entity
 *
 * @property int $id
 * @property int $parent_id
 * @property int $lft
 * @property int $rght
 * @property string $name
 * @property bool $active
 *
 * @property \App\Model\Entity\ParentDepartement $parent_departement
 * @property \App\Model\Entity\ChildDepartement[] $child_departements
 * @property \App\Model\Entity\User[] $users
 */
class Departement extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
