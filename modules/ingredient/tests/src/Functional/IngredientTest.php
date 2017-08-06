<?php

namespace Drupal\Tests\ingredient\Functional;

use Drupal\Core\URL;
use Drupal\ingredient\Entity\Ingredient;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests Ingredient CRUD functions.
 *
 * @group recipe
 */
class IngredientTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['block', 'ingredient', 'field_ui', 'views'];

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
    $web_user = $this->drupalCreateUser([
      'add ingredient',
      'edit ingredient',
      'view ingredient',
      'delete ingredient',
      'administer ingredient',
      'administer ingredient display',
      'administer ingredient fields',
      'administer ingredient form display',
    ]);

    $this->drupalLogin($web_user);

    // Web_user user has the right to view listing.
    $this->drupalGet('admin/content/ingredient');

    // WebUser can add entity content.
    $this->assertSession()->linkExists('Add Ingredient');

    $this->clickLink(t('Add Ingredient'));

    $this->assertSession()->fieldExists('name[0][value]');

    // Post content, save an instance. Go back to list after saving.
    $edit = [
      'name[0][value]' => 'test name',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');

    // Entity listed.
    $this->assertSession()->linkExists('Edit');
    $this->assertSession()->linkExists('Delete');

    $this->clickLink('test name');

    // Entity shown.
    $this->assertSession()->pageTextContains('test name');
    $this->assertSession()->pageTextContains('Edit');
    $this->assertSession()->pageTextContains('Delete');

    // Check for the breadcrumb.
    $expected_breadcrumb = [];
    $expected_breadcrumb[] = URL::fromRoute('<front>')->toString();
    $expected_breadcrumb[] = URL::fromRoute('ingredient.landing_page')->toString();

    // Fetch links in the current breadcrumb.
    $links = $this->xpath('//nav[@class="breadcrumb"]/ol/li/a');
    $got_breadcrumb = [];
    foreach ($links as $link) {
      $got_breadcrumb[] = $link->getAttribute('href');
    }

    // Compare expected and got breadcrumbs.
    $this->assertSame($expected_breadcrumb, $got_breadcrumb, 'The breadcrumb is correctly displayed on the page.');

    // Delete the entity.
    $this->clickLink('Delete');

    // Confirm deletion.
    $this->assertSession()->linkExists('Cancel');
    $this->drupalPostForm(NULL, [], 'Delete');

    // Back to list, must be empty.
    $this->assertSession()->pageTextNotContains('test name');

    // Settings page.
    $this->drupalGet('admin/structure/ingredient_settings');
    $this->assertSession()->pageTextContains('Ingredient Settings');

    // Make sure the field manipulation links are available.
    $this->assertSession()->linkExists('Settings');
    $this->assertSession()->linkExists('Manage fields');
    $this->assertSession()->linkExists('Manage form display');
    $this->assertSession()->linkExists('Manage display');
  }

  /**
   * Test all paths exposed by the module, by permission.
   */
  public function testPaths() {
    // Generate an ingredient so that we can test the paths against it.
    $ingredient = Ingredient::create(
      [
        'name' => 'test name',
      ]
    );
    $ingredient->save();

    // Gather the test data.
    $data = $this->providerTestPaths($ingredient->id());

    // Run the tests.
    foreach ($data as $datum) {
      // drupalCreateUser() doesn't know what to do with an empty permission
      // array, so we help it out.
      if ($datum[2]) {
        $user = $this->drupalCreateUser([$datum[2]]);
        $this->drupalLogin($user);
      }
      else {
        $user = $this->drupalCreateUser();
        $this->drupalLogin($user);
      }
      $this->drupalGet($datum[1]);
      $this->assertSession()->statusCodeEquals($datum[0]);
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
    return [
      [
        200,
        '/ingredient/' . $ingredient_id,
        'view ingredient',
      ],
      [
        403,
        '/ingredient/' . $ingredient_id,
        '',
      ],
      [
        200,
        '/admin/content/ingredient',
        'administer ingredient',
      ],
      [
        403,
        '/admin/content/ingredient',
        '',
      ],
      [
        200,
        '/ingredient/add',
        'add ingredient',
      ],
      [
        403,
        '/ingredient/add',
        '',
      ],
      [
        200,
        '/ingredient/' . $ingredient_id . '/edit',
        'edit ingredient',
      ],
      [
        403,
        '/ingredient/' . $ingredient_id . '/edit',
        '',
      ],
      [
        200,
        '/ingredient/' . $ingredient_id . '/delete',
        'delete ingredient',
      ],
      [
        403,
        '/ingredient/' . $ingredient_id . '/delete',
        '',
      ],
      [
        200,
        'admin/structure/ingredient_settings',
        'administer ingredient',
      ],
      [
        403,
        'admin/structure/ingredient_settings',
        '',
      ],
    ];
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
    $this->assertSession()->checkboxChecked('edit-ingredient-name-normalize-0');

    // Add a new ingredient with capitalized characters in the name.
    $edit = [
      'name[0][value]' => 'TeSt InGrEdIeNt 1',
    ];
    $this->drupalPostForm('ingredient/add', $edit, 'Save');
    // Verify that the name did not change on save.
    $this->assertSession()->pageTextContains('TeSt InGrEdIeNt 1');

    // Turn ingredient normalization on.
    $edit = [
      'ingredient_name_normalize' => 1,
    ];
    $this->drupalPostForm('admin/structure/ingredient_settings', $edit, 'Save configuration');

    // Add a new ingredient with capitalized characters in the name.
    $edit = [
      'name[0][value]' => 'TeSt InGrEdIeNt 2',
    ];
    $this->drupalPostForm('ingredient/add', $edit, 'Save');
    // Verify that the name was normalized on save.
    $this->assertSession()->pageTextContains('test ingredient 2');

    // Add a new ingredient with capitalized characters and an &reg; symbol in
    // the name.
    $edit = [
      'name[0][value]' => 'TeSt InGrEdIeNt 3 ®',
    ];
    $this->drupalPostForm('ingredient/add', $edit, 'Save');
    // Verify that the name was not normalized on save.
    $this->assertSession()->pageTextContains('TeSt InGrEdIeNt 3 ®');

    // Turn ingredient normaliation back off.
    $edit = [
      'ingredient_name_normalize' => 0,
    ];
    $this->drupalPostForm('admin/structure/ingredient_settings', $edit, 'Save configuration');

    // Add a new ingredient with capitalized characters in the name.
    $edit = [
      'name[0][value]' => 'TeSt InGrEdIeNt 4',
    ];
    $this->drupalPostForm('ingredient/add', $edit, 'Save');
    // Verify that the name did not change on save.
    $this->assertSession()->pageTextContains('TeSt InGrEdIeNt 4');
  }

}
