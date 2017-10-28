<?php
/**
 * Plugin Name: WP Content Pilot Seeder
 * Description: Generates dummy data for your WP Content Pilot
 * Plugin URI: http://pluginever.com
 * Author: MD Sultan Nasir Uddin
 * Author URI: http://pluginever.com
 * Version: 1.0
 * License: GPL2
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

require_once 'vendor/autoload.php';

/**
 * The plugin
 */
class WPCP_Seeder {

    private $faker;
    private $employee_count;
    private $customer_count;

    /**
     * Constructor
     */
    public function __construct() {

        $this->employee_count = 20;
        $this->customer_count = 20;
        $this->faker = Faker\Factory::create();

        $this->faker->addProvider( new Faker\Provider\en_US\Person($this->faker ) );
        $this->faker->addProvider( new Faker\Provider\Internet( $this->faker ) );
        $this->faker->addProvider( new Faker\Provider\Base( $this->faker ) );
        $this->faker->addProvider( new Faker\Provider\DateTime( $this->faker ) );
        $this->faker->addProvider( new Faker\Provider\en_US\Address( $this->faker ) );
        $this->faker->addProvider( new Faker\Provider\en_US\PhoneNumber( $this->faker ) );
        $this->faker->addProvider( new Faker\Provider\Miscellaneous( $this->faker ) );

        $this->file_includes();
    }

    /**
     * Initializes the WPCP_Seeder() class
     *
     * Checks for an existing WPCP_Seeder() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Seeder files
     *
     * @return void
     */
    public function file_includes() {

        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            include_once __DIR__ . '/includes/cli/class-commands.php';
        }
    }
}

function erp_seeder() {
    return WPCP_Seeder::init();
}

erp_seeder();