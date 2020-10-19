<?php

namespace Drupal\corpus_migrate\Plugin\migrate\source\d8;

use Drupal\corpus_migrate\Plugin\migrate\source\d8\ContentEntity;
use Drupal\migrate\Row;

/**
 * Drupal 8 custom user source from database.
 *
 * @MigrateSource(
 *   id = "d8_custom_user",
 *   source_provider = "corpus_migrate"
 * )
 */
class User extends ContentEntity {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $uid = $row->getSourceProperty('uid');
    $roles = $this->getRoles($uid);
    if (!empty($roles)) {
      $row->setSourceProperty('roles', $roles);
    }
    $tags = $this->select('user__field_institution', 'f')
      ->fields('f', ['field_institution_target_id'])
      ->condition('entity_id', $uid)
      ->execute()
      ->fetchCol();
    $row->setSourceProperty('tag_list', $tags);
    return parent::prepareRow($row);
  }

  /**
   * This allows obtaining all the user roles..
   *
   * @param int $uid
   *   The user id.
   *
   * @return array
   *   The roles of the user.
   */
  protected function getRoles($uid) {
    /** @var \Drupal\Core\Database\Query\SelectInterface $query */
    $query = $this->select('user__roles', 'r')
      ->fields('r', ['roles_target_id'])
      ->condition('entity_id', $uid);
    return array_column($query->execute()->fetchAll(), 'roles_target_id');
  }
}
