<?php

namespace Drupal\file_upload\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Tests the File Upload input fields.
 */
class FileUploadForm extends FormBase {

  /**
   * The Entity Type Manager interface service injected into this class.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The class constructor handles processing on object instantiation.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity Type Manager service injected into this Form class.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager
  ) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Handles the injection of services into the constructor.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container service that handles the services to inject.
   *
   * @return \Drupal\file_upload\Form\FileUploadForm|static
   *   The arguments to pass to the class constructor.
   */
  public static function create(
    ContainerInterface $container
  ) {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Returns the form ID.
   *
   * @return string
   *   The ID of the Form.
   */
  public function getFormId(): string {
    return 'test_file_upload_form';
  }

  /**
   * Builds the form elements.
   *
   * @param array $form
   *   The form parameter.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form_state parameter.
   *
   * @return array
   *   Returns the array of Form fields.
   */
  public function buildForm(
    array $form,
    FormStateInterface $form_state
  ): array {
    // Add the title.
    $form['collection']['test_file'] = [
      '#type'               => 'managed_file',
      '#name'               => 'test_file',
      '#title'              => t('File Upload'),
      '#description'        => t('This is a test file description.'),
      '#upload_validators'  => [
        'file_validate_extensions'  => [
          'pdf doc docx xls xlsx zip',
        ],
      ],
      '#upload_location'    => 'public://',
      '#required'           => TRUE,
      '#cardinality'        => 1,
    ];

    // Add a Save button.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value'  => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * Handles the form submission.
   *
   * @param array $form
   *   The form definition array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The Form State object, which includes input and value data.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function submitForm(
    array &$form,
    FormStateInterface $form_state
  ): void {
    $test_file_id = $form_state->getValue('test_file')[0];
    /** @var \Drupal\file\FileInterface $test_file */
    $test_file
      = $this
        ->entityTypeManager
        ->getStorage('file')
        ->load($test_file_id);

    $test_file->setPermanent();
    $test_file->save();

    // Generate a message to be shown to the user.
    $this->messenger()->addStatus('File successfully posted.');
  }

}
