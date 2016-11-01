<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Letter Entity
 *
 * @property int $id
 * @property int $sender_id
 * @property int $user_id
 * @property int $via_id
 * @property string $number
 * @property \Cake\I18n\Time $date
 * @property string $content
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property bool $isread
 * @property bool $active
 *
 * @property \App\Model\Entity\Sender $sender
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Via $via
 * @property \App\Model\Entity\Disposition[] $dispositions
 * @property \App\Model\Entity\Evidence[] $evidences
 */
class Letter extends Entity
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
