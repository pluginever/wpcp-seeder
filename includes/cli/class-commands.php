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
			if( $type == 'random' ){
				$type = $faker->randomElements(array_keys(wpcp_get_campaign_types()))[0];
			}
	        update_post_meta( $post_id, '_wpcp_campaign_type', $type);
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

			$this->populate_campaign_specific_contents( $post_id, $type );





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
			    	$links = $this->get_feed_link();
				    $link = $links[mt_rand(0, count($links) - 1)];
				    $feed_links[] = untrailingslashit(esc_url_raw($link));
			    }

			    $string_feed_links = implode(PHP_EOL, $feed_links);
			    update_post_meta( $post_id, '_wpcp_feed_links', $string_feed_links);
			    update_post_meta( $post_id, '_wpcp_force_feed', '1');
				//@todo add premimum data

			    break;
		    case 'articles':
			    $words = [];
			    for ($i=1; $i <= $size; $i++ ){
				    $words[] = str_replace('.', '', sanitize_text_field($faker->realText($maxNbChars = 20, $indexSize = 1)));
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

    function get_feed_link(){
    	$links = array(
		    'http://rss.cnn.com/rss/cnn_topstories.rss',
		    'http://feeds.nytimes.com/nyt/rss/HomePage',
			'http://www.washingtonpost.com/rss/',
			'http://hosted.ap.org/lineups/USHEADS-rss_2.0.xml?SITE=RANDOM&SECTION=HOME',
			'http://rssfeeds.usatoday.com/usatoday-NewsTopStories',
			'http://www.npr.org/rss/rss.php?id=1001',
			'http://feeds.reuters.com/reuters/topNews',
			'http://newsrss.bbc.co.uk/rss/newsonline_world_edition/americas/rss.xml',
			'http://www.sltrib.com/rss/feed/?sec=/News/Utah/&level=1',
			'http://www.deseretnews.com/site/rss',
			'http://www.ksl.com/xml/148.rss',
			'http://www.utah.gov/whatsnew/rss.xml',
			'http://rssfeeds.thespectrum.com/stgeorge/news',
			'http://topics.nytimes.com/top/news/national/usstatesterritoriesandpossessions/utah/index.html?inline=nyt-geo&rss=1',
			'http://www.uen.org/feeds/rss/news.xml.php',
			'https://www.ed.gov/feed',
			'http://www.smartbrief.com/servlet/rss?b=ASCD',
			'http://www.npr.org/rss/rss.php?id=1013',
			'http://www.techlearning.com/RSS',
			'http://hosted.ap.org/lineups/SCIENCEHEADS-rss_2.0.xml?SITE=OHLIM&SECTION=HOME',
			'http://feeds.sciencedaily.com/sciencedaily',
			'http://feeds.nature.com/nature/rss/current',
			'http://www.nasa.gov/rss/image_of_the_day.rss',
			'http://www.techlearning.com/RSS',
			'http://feeds.wired.com/wired/index',
			'http://feeds.nytimes.com/nyt/rss/Technology',
			'http://www.npr.org/rss/rss.php?id=1019',
			'http://feeds.feedburner.com/time/gadgetoftheweek',
			'http://feeds.surfnetkids.com/SurfingTheNetWithKids',
			'http://rss.macworld.com/macworld/feeds/main',
			'http://feeds.pcworld.com/pcworld/latestnews',
			'http://www.techworld.com/news/rss',
			'http://feeds.feedburner.com/FrontlineEditorsNotes',
			'http://www.pbs.org/wgbh/nova/rss/nova.xml',
			'http://dictionary.reference.com/wordoftheday/wotd.rss',
			'http://hosted.ap.org/lineups/SPORTSHEADS-rss_2.0.xml?SITE=VABRM&SECTION=HOME',
			'http://www.si.com/rss/si_topstories.rss',
			'http://feeds1.nytimes.com/nyt/rss/Sports',
			'http://www.nba.com/jazz/rss.xml',
			'http://www.npr.org/rss/rss.php?id=1008',
			'http://www.newyorker.com/feed/humor',
			'http://www.npr.org/rss/rss.php?id=13',
			'http://www.npr.org/rss/rss.php?id=1045',
			'http://www.nationalgeographic.com/adventure/nga.xml',
			'http://feeds.feedburner.com/TheDailyPuppy',
			'http://davidkphotography.com/index.php?x=rss',
			'http://feeds.feedburner.com/animals',
	    );

	    return $links;
    }


}

WP_CLI::add_command( 'wpcp-seeder', 'WPCP_Seeder_Command' );