<?php

use Drupal\file\Entity\File;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * A simple test class to check the file upload process.
 */
class FileUploadTest extends WebDriverTestBase {
  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The modules to load to run the test.
   *
   * @var array
   */
  protected static array $modules = [
    'user',
    'file',
    'path',
    'file_upload',
  ];

  /**
   * The test function to test file uploads.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function testManagedFileUpload() {

    // Navigate to the form.
    $this->drupalGet('/tests/file_upload');

    // Create a new text file.
    $filename = 'test.txt';
    $file_stream_path = 'public://' . $filename;

    // Create a media asset.
    file_put_contents(
      $file_stream_path,
      str_repeat('t', 10)
    );
    $file = File::create([
      'uri' => $file_stream_path,
      'filename' => $filename,
    ]);
    $file->save();

    $filepath
      = \Drupal::service('file_system')->realpath($file->getFileUri());

    // Attach the new file to the form field.
    $this->getSession()->getPage()->attachFileToField(
      'files[test_file]',
      $filepath
    );

    // Wait for the attachment to finish.
    $this->assertSession()->assertWaitOnAjaxRequest(
      10000,
      'Unable to complete AJAX request for the Test File field.'
    );

    // Submit the form.
    $this->getSession()->getPage()->pressButton('Save');

    // Check the result.
    $this->assertNotFalse(
      $this->assertSession()->waitForText('File successfully posted')
    );

    $shaun = true;

  }

}
