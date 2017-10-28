<?php
/**
 * WPCP_Seeder_Command class
 */
class WPCP_Seeder_Command extends WP_CLI_Command {

    /**
     * Seed the tables
     *
     * ## EXAMPLES
     *
     * wp erp-seeder seed customer --num=2000
     *
     * @synopsis <type> [--num=<number>]
     */
    public function seed( $args, $assoc_args ) {
        list( $name ) = $args;
        $type         = strtolower( $name );
        $number       = isset( $assoc_args['num'] ) ? $assoc_args['num'] : 0;

        for ( $i = 1 ; $i <= $number ; $i++ ){

	        $post_id = wp_insert_post(
		        array(
			        'post_title' => 'Campaign '. $i,
			        'post_status' => 'publish',
			        'post_type' => 'wp_content_pilot',
		        )
	        );

	        if( !$post_id  ) continue;

	        $faker = Faker\Factory::create();
			$campaign_type = $faker->randomElements(array_keys(wpcp_get_campaign_types()));
	        update_post_meta( $post_id, '_wpcp_campaign_type', $campaign_type[0]);
	        update_post_meta( $post_id, '_wpcp_active', 'on');
	        update_post_meta( $post_id, '_wpcp_campaign_target', $faker->numberBetween($min = 1, $max = 5));
	        update_post_meta( $post_id, '_wpcp_frequency_period', $faker->randomElements(['hour', 'day'])[0]);
	        update_post_meta( $post_id, '_wpcp_content_type', $faker->randomElements(['html', 'text'])[0]);

	        update_post_meta( $post_id, '_wpcp_first_image_featured', $faker->randomElements(['on', 'no'])[0]);
	        update_post_meta( $post_id, '_wpcp_skip_if_no_image', $faker->randomElements(['on', 'no'])[0]);
	        update_post_meta( $post_id, '_wpcp_remove_all_image', $faker->randomElements(['on', 'no'])[0]);
	        update_post_meta( $post_id, '_wpcp_skip_if_not_eng', $faker->randomElements(['on', 'no'])[0]);
	        update_post_meta( $post_id, '_wpcp_post_summary', $faker->randomElements(['on', 'no'])[0]);
	        update_post_meta( $post_id, '_wpcp_strip_links', $faker->randomElements(['on', 'no'])[0]);







        }



        WP_CLI::success( $type );
        WP_CLI::success( $number );
        WP_CLI::success( "Tables are successfully seeded!" );
    }
}

WP_CLI::add_command( 'wpcp-seeder', 'WPCP_Seeder_Command' );