<?php

namespace Drupal\custom_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\StreamWrapper\PublicStream;


/**
 * Class ExportForm.
 */
class ExportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_api_export_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Fetch all fields of the node type.
    $fields = [
      'title' => $this->t('Title'),
      'field_color' => $this->t('Color'),
      'field_capacity' => $this->t('Capacity'),
    ];

    $form['fields'] = [
      '#type' => 'select',
      '#title' => $this->t('Select fields to export'),
      '#options' => $fields,
      '#multiple' => TRUE,
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Prepare batch processing to handle large data exports.
    $fields = $form_state->getValue('fields');
    $batch = [
      'title' => $this->t('Exporting CSV'),
      'operations' => [
        [
          [$this, 'batchProcess'],
          [$fields],
        ],
      ],
      'finished' => [$this, 'batchFinished']
    ];
    batch_set($batch);
  }

  /**
   * Batch process callback.
   */
  public function batchProcess(array $fields, &$context) {
    // Initialize sandbox on the first run.
    if (empty($context['sandbox'])) {
      // Initialize sandbox variables.
      $context['sandbox']['progress'] = 0;
      $context['sandbox']['max'] = \Drupal::entityQuery('node')->accessCheck(TRUE)->count()->execute();
      $context['sandbox']['file_path'] = 'public://exported_data.csv';

      // Ensure the file path is valid.
      $file_path = \Drupal::service('file_system')->realpath($context['sandbox']['file_path']);
     
      // Attempt to open the file for writing.
      $file = fopen($file_path, 'w');
      if ($file === FALSE) {
        throw new \Exception('File could not be opened for writing.');
      }

      // Store the file handle and write the CSV header.
      $context['sandbox']['file'] = $file;
      $header = array_map(function ($field) {
        return $this->t($field);
      }, $fields);
      fputcsv($context['sandbox']['file'], $header);
    }
    else {
      // If the file pointer is lost, reopen the file in append mode.
      if (!is_resource($context['sandbox']['file'])) {
        $file_path = \Drupal::service('file_system')->realpath($context['sandbox']['file_path']);
        $file = fopen($file_path, 'a');
        if ($file === FALSE) {
          throw new \Exception('File could not be reopened for writing.');
        }
        $context['sandbox']['file'] = $file;
      }
    }

    // Process nodes and write data to the CSV.
    $nids = \Drupal::entityQuery('node')
      ->accessCheck(TRUE)
      ->condition('type', 'custom_type')  // Replace with your content type.
      ->range($context['sandbox']['progress'], 10)
      ->execute();

    $nodes = Node::loadMultiple($nids);

    foreach ($nodes as $node) {
      $row = [];
      foreach ($fields as $field) {
        $row[] = $node->get($field)->value;
      }
      fputcsv($context['sandbox']['file'], $row);
    }

    $context['sandbox']['progress'] += count($nids);
    if ($context['sandbox']['progress'] >= $context['sandbox']['max']) {
      fclose($context['sandbox']['file']);
      unset($context['sandbox']['file']); // Remove the file handle after closing.
    }

    // Update the completion percentage.
    //$context['finished'] = ($context['sandbox']['progress'] / $context['sandbox']['max']);
  }

  /**
   * Batch finished callback.
   */
  public function batchFinished($success, $results, $operations) {
   
    if ($success) {
      // Provide a link to download the CSV file.
      $public_path = PublicStream::basePath();
  
      $file_path = $public_path.'/exported_data.csv';
    
      \Drupal::messenger()->addMessage(t('CSV export completed.<a href=/'.$file_path. '>Dowload the file </a>'), 'status', TRUE); 
    }
    else {
      \Drupal::messenger()->addMessage($this->t('Export process failed.'), 'status');
    }
  }

}