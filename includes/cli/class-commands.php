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
     * wp wpcp-seeder seed random --num=10
     *
     * @synopsis <type> [--num=<number>]
     */
    public function seed( $args, $assoc_args ) {
	    list( $name ) = $args;
	    $type         = strtolower( $name );
	    $number       = isset( $assoc_args['num'] ) ? $assoc_args['num'] : 0;

	    $faker = Faker\Factory::create('en_US');
	    for ( $i = 1 ; $i <= $number ; $i++ ){

	        $post_id = wp_insert_post(
		        array(
			        'post_title' => $faker->sentence(4, true),
			        'post_status' => 'publish',
			        'post_type' => 'wp_content_pilot',
		        )
	        );

	        if( !$post_id  ) continue;

	        $campaign_type = $faker->randomElements(array_keys(wpcp_get_campaign_types()));
	        update_post_meta( $post_id, '_wpcp_campaign_type', $campaign_type[0]);
	        update_post_meta( $post_id, '_wpcp_active', $faker->numberBetween(0, 1));
	        update_post_meta( $post_id, '_wpcp_campaign_target', $faker->numberBetween($min = 1, $max = 5));
	        update_post_meta( $post_id, '_wpcp_frequency', $faker->numberBetween($min = 1, $max = 5));
	        update_post_meta( $post_id, '_wpcp_frequency_period', $faker->randomElements(['hour', 'day'])[0]);
	        update_post_meta( $post_id, '_wpcp_content_type', $faker->randomElements(['html', 'text'])[0]);

	        update_post_meta( $post_id, '_wpcp_first_image_featured', $faker->numberBetween(0, 1));
	        update_post_meta( $post_id, '_wpcp_skip_if_no_image', $faker->numberBetween(0, 1));
	        update_post_meta( $post_id, '_wpcp_remove_all_image', $faker->numberBetween(0, 1));
	        update_post_meta( $post_id, '_wpcp_skip_if_not_eng', $faker->numberBetween(0, 1));
	        update_post_meta( $post_id, '_wpcp_post_summary', $faker->numberBetween(0, 1));
	        update_post_meta( $post_id, '_wpcp_strip_links', $faker->numberBetween(0, 1));


	        update_post_meta( $post_id, '_wpcp_last_run', $faker->unixTime($max = 'now'));


	        //post settings
		    update_post_meta( $post_id, '_wpcp_post_type', $faker->randomElement(array_keys(wpcp_supported_get_post_types()))[0]);
		    update_post_meta( $post_id, '_wpcp_post_author', $faker->randomElement(array_keys(wpcp_get_authors()))[0]);

	        //advance
		    update_post_meta( $post_id, '_wpcp_title_limit', $faker->numberBetween($min = 40, $max = 80));
		    update_post_meta( $post_id, '_wpcp_content_limit', $faker->numberBetween($min = 500, $max = 2000));

			$this->populate_campaign_specific_contents( $post_id, $campaign_type[0]);





        }



        WP_CLI::success( $type );
        WP_CLI::success( $number );
        WP_CLI::success( "Tables are successfully seeded!" );
    }


    function populate_campaign_specific_contents ($post_id, $type) {
	    $faker = Faker\Factory::create('en_US');
	    $size = $faker->numberBetween($min = 5, $max = 10);

	    switch ($type){
		    case 'feeds':
			    $feed_links = [];
			    for ($i=1; $i <= $size; $i++ ){
				    $link = $faker->url;
				    $parsed_url = parse_url($link);
				    $feed_links[] = untrailingslashit(esc_url_raw($parsed_url['host']));
			    }

			    $string_feed_links = implode(PHP_EOL, $feed_links);
			    update_post_meta( $post_id, '_wpcp_feed_links', $string_feed_links);
				//@todo add premimum data

			    break;
		    case 'articles':
			    $words = [];
			    for ($i=1; $i <= $size; $i++ ){
				    $words[] = str_replace('.', '', sanitize_title($faker->realText($maxNbChars = 20, $indexSize = 1)));
			    }
			    $keywords = implode(PHP_EOL, $words);
			    update_post_meta( $post_id, '_wpcp_keywords', $keywords);
			    update_post_meta( $post_id, '_wpcp_article_source', $faker->randomElement(array_keys(wpcp_get_article_sources()))[0]);

			    break;
		    case 'youtube':
			    $words = [];
			    for ($i=1; $i <= $size; $i++ ){
				    $words[] = str_replace('.', '', sanitize_title($faker->realText($maxNbChars = 20, $indexSize = 1)));
			    }
			    $keywords = implode(PHP_EOL, $words);
			    update_post_meta( $post_id, '_wpcp_keywords', $keywords);
			    break;
		    case 'flicker':
			    $words = [];
			    for ($i=1; $i <= $size; $i++ ){
				    $words[] = str_replace('.', '', sanitize_title($faker->realText($maxNbChars = 20, $indexSize = 1)));
			    }
			    $keywords = implode(PHP_EOL, $words);
			    update_post_meta( $post_id, '_wpcp_keywords', $keywords);
			    break;








		    	break;



		    default:

			    break;
	    }


    }


}

WP_CLI::add_command( 'wpcp-seeder', 'WPCP_Seeder_Command' );