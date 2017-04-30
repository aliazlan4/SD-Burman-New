<?php
    function songs_catalogue(){
        global $wpdb;
        $data = getResultsForView();

        ?>
            <div class="row sorter_row">
                <form class="form-inline">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="sort_by_list">Sort by: </label>
                            <select class="form-group" id="sort_by_list">
                                <option value="movie">Movie</option>
                                <option value="song">Song</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-1">
                        <div class="form-group">
                            <label class="radio-inline">
                                <input type="radio" name="sorter_radio_option" id="sorter_radio_option1" value="alpha"> Alphabetically
                            </label>
                            <img style="max-width:25px" src="<?php echo plugins_url( '/images/sorter.png' , __FILE__ ); ?>">
                            <label class="radio-inline">
                                <input type="radio" name="sorter_radio_option" id="sorter_radio_option2" value="year"> Year
                            </label>
                            <img style="max-width:25px" src="<?php echo plugins_url( '/images/sorter.png' , __FILE__ ); ?>">
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-3">
                        <input type="text" class="form-control" id="search_song" placeholder="Search">
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-md-2">
                    Filter Results
                </div>
                <div class="col-md-10">
                    <?php
                        foreach ($data as $movie) {
                            ?>
                                <div class="row catalogue_colomn">
                                    <div class="text-center embed-responsive embed-responsive-16by9" style="display:none; margin:20px;">
                                        <iframe class="embed-responsive-item" id="player_<?php echo $movie["id"]; ?>" width="80%" height="400px" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                    <div class="col-md-2">
                                        <img width="125px" height="150px" src="http://c.saavncdn.com/001/S-D-Burman-The-Evergreen-Composer-2013-500x500.jpg">
                                    </div>
                                    <div class="col-md-10">
                                        <h5><?php echo $movie["name"]; ?></h5>
                                        <table class="song_detail_table" id="song_detail_table_<?php echo $movie["id"]; ?>">
                                            <tr>
                                                <td>Language</td><td> : </td><td id="song_detail_language_<?php echo $movie["id"] ?>">Urdu/Bengali</td>
                                            </tr>
                                            <tr>
                                                <td>Genre</td><td> : </td><td id="song_detail_genre_<?php echo $movie["id"] ?>">Motherhood</td>
                                            </tr>
                                            <tr>
                                                <td>Year</td><td> : </td><td id="song_detail_year_<?php echo $movie["id"] ?>">1990</td>
                                            </tr>
                                        </table></br>
                                        <table class="table table-hover table_songs">
                                            <?php
                                                $count = 1;
                                                foreach ($movie["songs"] as $song) {

                                                    ?>
                                                        <tr onclick="catalogue_play_song(<?php echo $movie["id"]; ?>, '<?php echo $song["url"]; ?>', '<?php echo getLanguage($song["language"]); ?>', '<?php echo getGenre($song["genre"]); ?>', '<?php echo $song["year"] ?>')"><td>
                                                            <?php echo $count++ . ". " . $song["name"]; ?>

                                                            <div style="float: right;">
                                                                <img width="20px" src="<?php echo plugins_url( '/images/play_count.png' , __FILE__ ); ?>">
                                                                <?php echo getVideoViews($song["url"]); ?>
                                                            </div>
                                                        </td></tr>
                                                    <?php
                                                }
                                            ?>
                                        </table>
                                    </div>
                                </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
        <?php
    }
    add_shortcode( 'codistan_songs_catalogue', 'songs_catalogue' );

    function getResultsForView(){
        global $wpdb;

        $result = array();
        $movies = $wpdb->get_results("SELECT * FROM codistan_movies");

        foreach ($movies as $movie) {
            $number_of_songs = $wpdb->get_var("SELECT COUNT(*) FROM codistan_songs WHERE movie=" . $movie->id . " AND status=true");
            if($number_of_songs > 0){
                $songs = $wpdb->get_results("SELECT * FROM codistan_songs WHERE movie = " . $movie->id . " AND status=true");
                $all_songs = array();
                foreach ($songs as $song) {
                    array_push($all_songs, array("id"=>$song->id, "name"=>$song->name, "type"=>$song->song_type, "language"=>$song->language, "genre"=>$song->genre, "url"=>$song->media_url, "year"=>$song->year));
                }
                $new_movie = array("id"=>$movie->id, "name"=>$movie->name, "director"=>$movie->director, "year"=>$movie->year, "actors"=>$movie->actors, "songs"=>$all_songs);
                array_push($result, $new_movie);
            }
        }

        $number_of_songs = $wpdb->get_var("SELECT COUNT(*) FROM codistan_songs WHERE song_type=1 AND status=true");
        if($number_of_songs > 0){
            $songs = $wpdb->get_results("SELECT * FROM codistan_songs WHERE song_type=1 AND status=true");
            $all_songs = array();
            foreach ($songs as $song) {
                array_push($all_songs, array("id"=>$song->id, "name"=>$song->name, "type"=>$song->song_type, "language"=>$song->language, "genre"=>$song->genre, "url"=>$song->media_url, "year"=>$song->year));
            }
            $solo_songs = array("id"=>0, "name"=>"-Solo Songs", "songs"=>$all_songs);
            array_push($result, $solo_songs);
        }
        return $result;
    }
?>
