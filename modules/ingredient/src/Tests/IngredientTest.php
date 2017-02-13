<?php

namespace Drupal\ingredient\Tests;

use Drupal\Core\URL;
use Drupal\ingredient\Entity\Ingredient;
use Drupal\simpletest\WebTestBase;

/**
 * Tests Ingredient CRUD functions.
 *
 * @group recipe
 */
class IngredientTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('block', 'ingredient', 'field_ui', 'views');

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Install Drupal.
    parent::setUp();
    // Add the system menu blocks to appropriate regions.
    $this->setupIngredientMenus();
    // Add the system breadcrumb block.
    $this->drupalPlaceBlock('system_breadcrumb_block');
  }

  /**
   * Set up menus and tasks in their regions.
   */
  protected function setupIngredientMenus() {
    $this->drupalPlaceBlock('local_tasks_block', ['region' => 'secondary_menu']);
    $this->drupalPlaceBlock('local_actions_block', ['region' => 'content']);
    $this->drupalPlaceBlock('page_title_block', ['region' => 'content']);
  }

  /**
   * Basic tests for Content Entity Example.
   */
  public function testIngredient() {
    $web_user = $this->drupalCreateUser(array(
      'add ingredient',
      'edit ingredient',
      'view ingredient',
      'delete ingredient',
      'administer ingredient',
      'administer ingredient display',
      'administer ingredient fields',
      'administer ingredient form display'));

    $this->drupalLogin($web_user);

    // Web_user user has the right to view listing.
    $this->drupalGet('admin/content/ingredient');

    // WebUser can add entity content.
    $this->assertLink(t('Add Ingredient'));

    $this->clickLink(t('Add Ingredient'));

    $this->assertFieldByName('name[0][value]', '', 'Name Field, empty');

    // Post content, save an instance. Go back to list after saving.
    $edit = array(
      'name[0][value]' => 'test name',
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Entity listed.
    $this->assertLink(t('Edit'));
    $this->assertLink(t('Delete'));

    $this->clickLink('test name');

    // Entity shown.
    $this->assertText(t('test name'));
    $this->assertLink(t('Edit'));
    $this->assertLink(t('Delete'));

    // Check for the breadcrumb.
    $expected_breadcrumb = [];
    $expected_breadcrumb[] = URL::fromRoute('<front>')->toString();
    $expected_breadcrumb[] = URL::fromRoute('ingredient.landing_page')->toString();

    // Fetch links in the current breadcrumb.
    $links = $this->xpath('//nav[@class="breadcrumb"]/ol/li/a');
    $got_breadcrumb = array();
    foreach ($links as $link) {
      $got_breadcrumb[] = (string) $link['href'];
    }

    // Compare expected and got breadcrumbs.
    $this->assertIdentical($expected_breadcrumb, $got_breadcrumb, 'The breadcrumb is correctly displayed on the page.');

    // Delete the entity.
    $this->clickLink('Delete');

    // Confirm deletion.
    $this->assertLink(t('Cancel'));
    $this->drupalPostForm(NULL, array(), 'Delete');

    // Back to list, must be empty.
    $this->assertNoText('test name');

    // Settings page.
    $this->drupalGet('admin/structure/ingredient_settings');
    $this->assertText(t('Ingredient Settings'));

    // Make sure the field manipulation links are available.
    $this->assertLink(t('Settings'));
    $this->assertLink(t('Manage fields'));
    $this->assertLink(t('Manage form display'));
    $this->assertLink(t('Manage display'));
  }

  /**
   * Test all paths exposed by the module, by permission.
   */
  public function testPaths() {
    // Generate an ingredient so that we can test the paths against it.
    $ingredient = Ingredient::create(
      array(
        'name' => 'test name',
      )
    );
    $ingredient->save();

    // Gather the test data.
    $data = $this->providerTestPaths($ingredient->id());

    // Run the tests.
    foreach ($data as $datum) {
      // drupalCreateUser() doesn't know what to do with an empty permission
      // array, so we help it out.
      if ($datum[2]) {
        $user = $this->drupalCreateUser(array($datum[2]));
        $this->drupalLogin($user);
      }
      else {
        $user = $this->drupalCreateUser();
        $this->drupalLogin($user);
      }
      $this->drupalGet($datum[1]);
      $this->assertResponse($datum[0]);
    }
  }

  /**
   * Data provider for testPaths.
   *
   * @param int $ingredient_id
   *   The id of an existing Ingredient entity.
   *
   * @return array
   *   Nested array of testing data. Arranged like this:
   *   - Expected response code.
   *   - Path to request.
   *   - Permission for the user.
   */
  protected function providerTestPaths($ingredient_id) {
    return array(
      array(
        200,
        '/ingredient/' . $ingredient_id,
        'view ingredient',
      ),
      array(
        403,
        '/ingredient/' . $ingredient_id,
        '',
      ),
      array(
        200,
        '/admin/content/ingredient',
        'administer ingredient',
      ),
      array(
        403,
        '/admin/content/ingredient',
        '',
      ),
      array(
        200,
        '/ingredient/add',
        'add ingredient',
      ),
      array(
        403,
        '/ingredient/add',
        '',
      ),
      array(
        200,
        '/ingredient/' . $ingredient_id . '/edit',
        'edit ingredient',
      ),
      array(
        403,
        '/ingredient/' . $ingredient_id . '/edit',
        '',
      ),
      array(
        200,
        '/ingredient/' . $ingredient_id . '/delete',
        'delete ingredient',
      ),
      array(
        403,
        '/ingredient/' . $ingredient_id . '/delete',
        '',
      ),
      array(
        200,
        'admin/structure/ingredient_settings',
        'administer ingredient',
      ),
      array(
        403,
        'admin/structure/ingredient_settings',
        '',
      ),
    );
  }

  /**
   * Test ingredient entity settings.
   */
  public function testIngredientSettings() {
    $web_user = $this->drupalCreateUser([
      'add ingredient',
      'edit ingredient',
      'view ingredient',
      'delete ingredient',
      'administer ingredient',
    ]);

    $this->drupalLogin($web_user);

    // Verify that ingredient normalization is off by default.
    $this->drupalGet('admin/structure/ingredient_settings');
    $this->assertFieldChecked('edit-ingredient-name-normalize-0', 'Ingredient normalization is off by default.');

    // Add a new ingredient with capitalized characters in the name.
    $edit = [
      'name[0][value]' => 'TeSt InGrEdIeNt 1',
    ];
    $this->drupalPostForm('ingredient/add', $edit, t('Save'));
    // Verify that the name did not change on save.
    $this->assertText('TeSt InGrEdIeNt 1', 'Found the ingredient name with capitalized characters.');

    // Turn ingredient normaliation on.
    $edit = [
      'ingredient_name_normalize' => 1,
    ];
    $this->drupalPostForm('admin/structure/ingredient_settings', $edit, t('Save configuration'));

    // Add a new ingredient with capitalized characters in the name.
    $edit = [
      'name[0][value]' => 'TeSt InGrEdIeNt 2',
    ];
    $this->drupalPostForm('ingredient/add', $edit, t('Save'));
    // Verify that the name was normalized on save.
    $this->assertText('test ingredient 2', 'Found the ingredient name with normalized characters.');

    // Add a new ingredient with capitalized characters and an &reg; symbol in
    // the name.
    $edit = [
      'name[0][value]' => 'TeSt InGrEdIeNt 3 ®',
    ];
    $this->drupalPostForm('ingredient/add', $edit, t('Save'));
    // Verify that the name was not normalized on save.
    $this->assertText('TeSt InGrEdIeNt 3 ®', 'Found the ingredient name with capitalized characters.');

    // Turn ingredient normaliation back off.
    $edit = [
      'ingredient_name_normalize' => 0,
    ];
    $this->drupalPostForm('admin/structure/ingredient_settings', $edit, t('Save configuration'));

    // Add a new ingredient with capitalized characters in the name.
    $edit = [
      'name[0][value]' => 'TeSt InGrEdIeNt 4',
    ];
    $this->drupalPostForm('ingredient/add', $edit, t('Save'));
    // Verify that the name did not change on save.
    $this->assertText('TeSt InGrEdIeNt 4', 'Found the ingredient name with capitalized characters.');
  }

}
