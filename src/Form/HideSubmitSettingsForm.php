<?php

namespace Drupal\hide_submit\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class HideSubmitSettingsForm
 *
 * Contains general admin settings for the Hide Submit module.
 *
 * @package Drupal\hide_submit\Form
 */
class HideSubmitSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hide_submit_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['hide_submit.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('hide_submit.settings');

    $form['hide_submit'] = ['#tree' => TRUE];

    $form['hide_submit']['method'] = array(
      '#type' => 'select',
      '#options' => array(
        'none' => $this->t('Do Nothing (disabled)'),
        'disable' => $this->t('Disable the Submit Buttons'),
        'hide' => $this->t('Hide the Submit Buttons'),
        'indicator' => $this->t('Built-in Loading Indicator'),
      ),
      '#default_value' => $config->get('method'),
      '#title' => $this->t('Blocking Method'),
      '#description' => $this->t('Choose the blocking method.'),
    );

    $form['hide_submit']['reset_time'] = array(
      '#type' => 'number',
      '#title' => $this->t('Reset Buttons After Some Time (ms).'),
      '#description' => $this->t('Enter a value in milliseconds after which all buttons will be enabled. To disable this enter 0.'),
      '#default_value' => $config->get('reset_time'),
      '#required' => TRUE,
      '#min' => 0,
    );

    $form['hide_submit']['disable'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Disabling Settings'),
      '#states' => [
        'visible' => [
          ':input[name="hide_submit[method]"]' => ['value' => 'disable'],
        ],
      ],
    );

    $form['hide_submit']['disable']['abtext'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Append to Buttons'),
      '#description' => $this->t('This text will be appended to each of the submit buttons.'),
      '#default_value' => $config->get('disable.abtext'),
    );

    $form['hide_submit']['disable']['atext'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Add Next to Buttons'),
      '#description' => $this->t('This text will be added next to the submit buttons.'),
      '#default_value' => $config->get('disable.atext'),
    );

    $form['hide_submit']['hide'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Hiding Settings'),
      '#states' => [
        'visible' => [
          ':input[name="hide_submit[method]"]' => ['value' => 'hide'],
        ],
      ],
    );

    $form['hide_submit']['hide']['hide_fx'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Use Fade Effects?'),
      '#default_value' => $config->get('hide.hide_fx'),
      '#description' => $this->t('Enabling a fade in / out effect.'),
    );

    $form['hide_submit']['hide']['hide_text'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Processing Text'),
      '#default_value' => $config->get('hide.hide_text'),
      '#description' => $this->t('This text will be shown to the user instead of the submit buttons.'),
    );

    $form['hide_submit']['indicator'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Indicator Settings'),
      '#states' => [
        'visible' => [
          ':input[name="hide_submit[method]"]' => ['value' => 'indicator'],
        ],
      ],
      '#description' => $this->t('Choose the spinner style as defined by the
      <a href="@library" target="_blank" rel="noopener">ladda.js jQuery library
      </a>. Examples of these styles can be found on the <a href="@examples"
      target="_blank" rel="noopener">Ladda example page</a>.', array(
        '@library' => '//github.com/hakimel/Ladda',
        '@examples' => '//lab.hakim.se/ladda/',
      )),
    );

    $form['hide_submit']['indicator']['indicator_style'] = array(
      '#type' => 'select',
      '#options' => array(
        'expand-left' => $this->t('Expand Left'),
        'expand-right' => $this->t('Expand Right'),
        'expand-up' => $this->t('Expand Up'),
        'expand-down' => $this->t('Expand Down'),
        'contract' => $this->t('Contract'),
        'contract-overlay' => $this->t('Contract Overlay'),
        'zoom-in' => $this->t('Zoom In'),
        'zoom-out' => $this->t('Zoom Out'),
        'slide-left' => $this->t('Slide Left'),
        'slide-right' => $this->t('Slide Right'),
        'slide-up' => $this->t('Slide Up'),
        'slide-down' => $this->t('Slide Down'),
      ),
      '#default_value' => $config->get('indicator.indicator_style'),
      '#title' => $this->t('Built-In Loading Indicator Style'),
    );

    // @todo: Can this be changed to an textfield to allow any color? OR a color picker?
    $form['hide_submit']['indicator']['spinner_color'] = array(
      '#type' => 'select',
      '#options' => array(
        '#000' => $this->t('Black'),
        '#A9A9A9' => $this->t('Dark Grey'),
        '#808080' => $this->t('Grey'),
        '#D3D3D3' => $this->t('Light Grey'),
        '#fff' => $this->t('White'),
      ),
      '#default_value' => $config->get('indicator.spinner_color'),
      '#title' => $this->t('Built-In Loading Indicator Spinner Color'),
    );

    $form['hide_submit']['indicator']['spinner_lines'] = array(
      '#type' => 'number',
      '#title' => $this->t('Number of Lines For the Spinner'),
      '#default_value' => $config->get('indicator.spinner_lines'),
      '#min' => 1,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('hide_submit.settings');
    $values = $form_state->getValues();

    foreach ($values['hide_submit'] as $field => $value) {
      $config->set($field, $value);
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
