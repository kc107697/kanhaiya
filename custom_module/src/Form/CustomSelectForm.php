<?php

namespace Drupal\custom_module\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class CustomSelectForm extends ConfigFormBase {

  /**
   *
   */
  protected function getEditableConfigNames() {
    return ['custom_config.settings'];
  }

  /**
   *
   */
  public function getFormId() {
    return 'custom_select_form';
  }

  /**
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('custom_config.settings');

    $form['company'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Company Details'),
      '#options' => [
        'name' => $this->t($config->get('name')),
        'company_id' => $this->t($config->get('company_id')),
        'location' => $this->t($config->get('location')),
        'sector' => $this->t($config->get('sector')),
      ],
      '#multiple' => TRUE,
    ];

    // $form['example_select'] = [
    //   '#type' => 'select',
    //   '#title' => $this->t('Select element'),
    //   '#options' => [
    //     '1' => $this->t($config->get('name')),
    //     '2' => $this->t($config->get('company_id')),
    //     '3' => $this->t($config->get('location')),
    //   ],
    // ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $selected_values = $form_state->getValue('company');

    \Drupal::logger('custom_module')->notice('Selected values: @values', ['@values' => implode(', ', $selected_values)]);
    $this->messenger()->addMessage($this->t('Selected values: @values', ['@values' => implode(', ', $selected_values)]));
  }

}
