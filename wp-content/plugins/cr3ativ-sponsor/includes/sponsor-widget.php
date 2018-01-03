<?php 

class cr3ativ_sponsor extends WP_Widget {

	// constructor
	function cr3ativ_sponsor() {
        parent::__construct(false, $name = __('Sponsor Loop', 'cr3at_sponsor') );
    }

	// widget form creation
	function form($instance) { 
 
 if( $instance) { 
     $title = esc_attr($instance['title']); 
     $sponsorlogo = esc_attr($instance['sponsorlogo']);
     $sponsorname = esc_attr($instance['sponsorname']);
     $sponsorlink = esc_attr($instance['sponsorlink']);
     $sponsorbio = esc_attr($instance['sponsorbio']);
     $orderby = esc_attr($instance['orderby']); 
     $order = esc_attr($instance['order']); 
     $itemstodisplay = esc_attr($instance['itemstodisplay']); 
     $cr3ativsponsor_level = esc_attr($instance['cr3ativsponsor_level']);
} else { 
     $title = ''; 
     $sponsorlogo = '';
     $sponsorname = '';
     $sponsorlink = '';
     $sponsorbio = '';
     $orderby = '';
     $order = '';
     $itemstodisplay = ''; 
     $cr3ativsponsor_level = '';
} 
?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'cr3at_sponsor'); ?></label>
<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" style="float:right; width:56%;" />
</p>
<p>
<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order by', 'cr3at_sponsor'); ?></label>
<select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>"  style="float:right; width:56%;">
    <option selected="selected" value="none"><?php _e( 'Select One', 'cr3at_sponsor' ); ?></option>
    <option <?php if ( $orderby == 'title' ) { echo ' selected="selected"'; } ?> value="title"><?php _e('Title', 'cr3at_sponsor'); ?></option>
    <option <?php if ( $orderby == 'date' ) { echo ' selected="selected"'; } ?> value="date"><?php _e('Date', 'cr3at_sponsor'); ?></option>
    <option <?php if ( $orderby == 'rand' ) { echo ' selected="selected"'; } ?> value="rand"><?php _e('Random', 'cr3at_sponsor'); ?></option>
</select>
</p>
<p>
<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Sorting Method', 'cr3at_sponsor'); ?></label>
<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>"  style="float:right; width:56%;">
    <option selected="selected" value="none"><?php _e( 'Select One', 'cr3at_sponsor' ); ?></option>
    <option <?php if ( $order == 'asc' ) { echo ' selected="selected"'; } ?> value="asc"><?php _e('ASC', 'cr3at_sponsor'); ?></option>
    <option <?php if ( $order == 'desc' ) { echo ' selected="selected"'; } ?> value="desc"><?php _e('DESC', 'cr3at_sponsor'); ?></option>
</select>
</p>
<p>
<label for="<?php echo $this->get_field_id('itemstodisplay'); ?>"><?php _e('How many to show?', 'cr3at_sponsor'); ?></label>
<input id="<?php echo $this->get_field_id('itemstodisplay'); ?>" name="<?php echo $this->get_field_name('itemstodisplay'); ?>" type="text" value="<?php echo $itemstodisplay; ?>" style="float:right; width:56%;" />
</p>

<p>
<label for="<?php echo $this->get_field_id('sponsorlogo'); ?>"><?php _e('Show sponsor logo?', 'cr3at_sponsor'); ?></label>
<input id="<?php echo $this->get_field_id('sponsorlogo'); ?>" name="<?php echo $this->get_field_name('sponsorlogo'); ?>" type="checkbox" value="1" <?php checked( '1', $sponsorlogo ); ?> style="float:right; margin-right:6px;" />
</p>
<p>
<label for="<?php echo $this->get_field_id('sponsorname'); ?>"><?php _e('Show sponsor name?', 'cr3at_sponsor'); ?></label>
<input id="<?php echo $this->get_field_id('sponsorname'); ?>" name="<?php echo $this->get_field_name('sponsorname'); ?>" type="checkbox" value="1" <?php checked( '1', $sponsorname ); ?> style="float:right; margin-right:6px;" />
</p>
<p>
<label for="<?php echo $this->get_field_id('sponsorlink'); ?>"><?php _e('Link logo and/or sponsor name?', 'cr3at_sponsor'); ?></label>
<input id="<?php echo $this->get_field_id('sponsorlink'); ?>" name="<?php echo $this->get_field_name('sponsorlink'); ?>" type="checkbox" value="1" <?php checked( '1', $sponsorlink ); ?> style="float:right; margin-right:6px;" />
</p>
<p>
<label for="<?php echo $this->get_field_id('sponsorbio'); ?>"><?php _e('Show sponsor bio?', 'cr3at_sponsor'); ?></label>
<input id="<?php echo $this->get_field_id('sponsorbio'); ?>" name="<?php echo $this->get_field_name('sponsorbio'); ?>" type="checkbox" value="1" <?php checked( '1', $sponsorbio ); ?> style="float:right; margin-right:6px;" />
</p>
<p>
<label for="<?php echo $this->get_field_id('cr3ativsponsor_level'); ?>"><?php _e('Sponsor Level', 'cr3at_sponsor'); ?></label>
<select id="<?php echo $this->get_field_id('cr3ativsponsor_level'); ?>" name="<?php echo $this->get_field_name('cr3ativsponsor_level'); ?>"  style="float:right; width:56%;" >
    <option selected="selected" value="none"><?php _e( 'Select One', 'cr3at_sponsor' ); ?></option>
    <?php $terms = get_terms( 'cr3ativsponsor_level' ); ?> 
    <option <?php if ( $cr3ativsponsor_level == 'all' ) { echo ' selected="selected"'; } ?> value="all"><?php _e( 'All', 'cr3at_sponsor' ); ?></option>
    <?php foreach ( $terms as $term ) { ?>
    <option<?php if ( $cr3ativsponsor_level == $term->slug ) { echo ' selected="selected"'; } ?> value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
    <?php } ?>
</select>
</p>

<?php }
	// widget update
	function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['orderby'] = $new_instance['orderby'];
      $instance['order'] = $new_instance['order'];
      $instance['sponsorlogo'] = $new_instance['sponsorlogo'];
      $instance['sponsorname'] = $new_instance['sponsorname'];
      $instance['sponsorlink'] = $new_instance['sponsorlink'];
      $instance['sponsorbio'] = $new_instance['sponsorbio'];
      $instance['itemstodisplay'] = $new_instance['itemstodisplay'];
      $instance['cr3ativsponsor_level'] = strip_tags($new_instance['cr3ativsponsor_level']);
     return $instance;
}

	// widget display
	function widget($args, $instance) {
   extract( $args );
   // these are the widget options
   $title = apply_filters('widget_title', $instance['title']);
   $sponsorlogo = $instance['sponsorlogo'];
   $sponsorname = $instance['sponsorname'];
   $sponsorlink = $instance['sponsorlink'];
   $sponsorbio = $instance['sponsorbio'];
   $itemstodisplay = $instance['itemstodisplay'];
   $cr3ativsponsor_level = $instance['cr3ativsponsor_level'];
   $orderby = $instance['orderby'];
   $order = $instance['order'];
   echo $before_widget;
      
global $post; 
        
    if( $cr3ativsponsor_level == ('none') ) { $cr3ativsponsor_level = 'all';}
    if( $orderby == ('none') ) { $orderby = 'rand';}
    if($order == ('none')) {$order = '';}
    if( $sponsorbio == ('null') ) { $sponsorbio = '';}
    if( $cr3ativsponsor_level != ('all') ) {      
		$args = array(
		'post_type' => 'cr3ativsponsor',
        'posts_per_page' => $itemstodisplay,
        'order' => $order,
        'orderby' => $orderby,
        'tax_query' => array(
            array(
                'taxonomy' => 'cr3ativsponsor_level',
                'field' => 'slug',
                'terms' => array( $cr3ativsponsor_level)
            )
        )); 
   } else {
		$args = array(
		'post_type' => 'cr3ativsponsor',
        'order' => $order,
        'orderby' => $orderby,
        'posts_per_page' => $itemstodisplay
		);
   }
   
    query_posts($args);  
   
   // Check if title is set
   if ( $title ) {
      echo $before_title . $title . $after_title;
   }	
   
   // Display the widget
?> 
		<?php 
   		if (have_posts($args)) : while (have_posts()) : the_post(); 
        $temp_title = get_the_title($post->ID);
        $temp_sponsorurl = get_post_meta($post->ID, 'cr3ativ_sponsorurl', $single = true);
        $temp_excerpt = get_the_content($post->ID);
        ?>
    
     <div class="cr3_sponsorwidget">
         <?php 
        
     if( $sponsorlogo == '1' ) { 
         if( $sponsorlink == '1' ) { ?>
         <a href="<?php echo ($temp_sponsorurl); ?>" target="_blank"><div class="cr3_sponsor_image"><?php the_post_thumbnail( 'full', array( 'class' => 'alignleft' ) ); ?></div></a>
     <?php ;} else { ?>
         <div class="cr3_sponsor_image"><?php the_post_thumbnail( 'full'); ?></div>
     <?php ;} 
         
     ;}
     if( $sponsorname == '1' ) { 
         if( $sponsorlink == '1' ) { ?>
         
            <h2 class="cr3_sponsorname"><a href="<?php echo ($temp_sponsorurl); ?>" target="_blank"><?php echo ($temp_title); ?></a></h2>
     <?php ;} else { ?>
             <h2 class="cr3_sponsorname"><?php echo ($temp_title); ?></h2>
     <?php ;} 
         
     ;} 

     if( $sponsorbio == '1' ) { ?>
         
         <p><?php echo ($temp_excerpt); ?></p>  
     <?php ;} ?>

        </div>
       
        <?php endwhile; ?>

        <?php else: ?> 
        <p><?php _e( 'There are no posts to display. Try using the search.', 'cr3at_sponsor' ); ?></p> 

        <?php endif; wp_reset_query(); ?>
  
<?php     
   
   echo $after_widget;
}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("cr3ativ_sponsor");'));

?>