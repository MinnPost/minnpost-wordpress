<?php
/**
 * Plugin Name: Merge duplicate terms
 * Description: Plugin for easy merging duplicate terms (tags, categories, ...)
 * Version: 1.1
 * Author: Zabatonni
 * Author URI: http://zabatonni.sk
 * License: GPLv2 only
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
if(!defined('ABSPATH')) { die; }

new zaba_merge_duplicate_terms;
class zaba_merge_duplicate_terms {
	private $donate='<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zabatonni@gmail.com&currency_code=USD&item_name=Merge+duplicate+terms+by+Zabatonni" title="Donations help me develop more plugins" target="_blank"><img src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_pp_142x27.png" alt="Donation"></a>';
	
	public function __construct() {
		add_filter('plugin_action_links_'.plugin_basename(__FILE__),array($this,'action_links'));
		add_action('admin_menu',array($this,'create_menu'));
		add_action('admin_init',array($this,'merge_it'));
	}
	
	public function action_links($links) {
		$links[]='<a href="'.admin_url('/tools.php?page=zaba-merge-terms').'">Merge terms</a>';
		$links[]=$this->donate;
		return $links;
	}
	
	public function create_menu() {
		add_management_page('Merge duplicate terms','Merge terms','administrator','zaba-merge-terms',array($this,'options_page'));
	}
	
	public function options_page() {
?>
<style>
.zaba-terms ul ul{margin:0 0 0 1em;}
.zaba-terms ul ul ul li{border-left:1px solid #cccccc;padding:0.5em 1em;margin:0;position:relative;}
.zaba-terms ul ul ul li:before{content:"";display:block;position:absolute;top:50%;left:0;height:1px;width:1em;background:#cccccc;}
</style>
<div class="wrap zaba-terms">
<h2>Merge duplicate terms</h2>
<p><?php echo $this->donate; ?></p>

<?php if(!isset($_POST['zaba_merge_taxs'])) : ?>
<?php
$taxs=get_taxonomies(array(),'objects');
unset($taxs['link_category'],$taxs['nav_menu'],$taxs['post_format']);
$post_types=get_post_types(array(),'objects');
?>
<p>Select which taxonomies you would like to search for duplicate terms.</p>
<form action="<?php echo admin_url('/tools.php?page=zaba-merge-terms&action=find-duplicates'); ?>" method="POST">
<?php
echo '<ul>';
foreach($taxs as $name=>$tax) {
	$cpt_relation=null;
	foreach($tax->object_type as $cpt) {
		$cpt_relation[]=$post_types[$cpt]->label;
	}
	echo '<li><label><input type="checkbox" name="zaba_merge_taxs[]" checked="checked" value="'.$name.'">'.$tax->label.' ('.implode(',',$cpt_relation).')</label></li>';
}
echo '</ul>';
?>
<input type="submit" class="button button-primary">
</form>
<?php else : ?>

<?php
$tax_duplicates=$this->find_duplicates($_POST['zaba_merge_taxs']);
if($tax_duplicates) :
?>
<p>Duplicate terms have been found! Consider <a href="https://codex.wordpress.org/Backing_Up_Your_Database" target="_blank">backing up your database</a> before any further actions.</p>
<p>Select which terms you would like to merge.</p>
<form action="<?php echo admin_url('/tools.php?page=zaba-merge-terms&action=merge-it'); ?>" method="POST">
<?php
echo '<ul>';
foreach($tax_duplicates as $tax=>$duplicates) {
	echo '<li><h3>'.get_taxonomy($tax)->label.'</h3>';
	foreach($duplicates as $orig=>$dups) {
		$tmp_data_orig=get_term($orig,$tax);
		echo '<ul><li><h4>'.$tmp_data_orig->name.' [ID: '.$orig.']</h4><ul>';
		foreach($dups as $dup) {
			$tmp_data=get_term($dup,$tax);
			echo '<li><label><input type="checkbox" name="zaba_merge_tax['.$tax.']['.$orig.'][]" value="'.$dup.'" checked="checked">'.$tmp_data->name.' [ID: '.$dup.']</label></li>';
		}
		echo '</ul></li></ul>';
	}
	echo '</li>';
}
echo '</ul>';
?>

<p><input type="submit" class="button button-primary" value="Merge them!"> <a href="?page=zaba-merge-terms" class="button button-secondary">Go Back</a></p>
</form>
<?php else: ?>
<p>There are no duplicate terms you could get merged.</p>
<p><a href="?page=zaba-merge-terms" class="button button-secondary">Go back</a></p>
<?php endif; ?>
<?php endif; ?>
</div>
<?php
	}
	
	public function merge_it() {
		if(isset($_GET['page']) && $_GET['page']=='zaba-merge-terms') {
			$exec_time=ini_get('max_execution_time');
			if((int)$exec_time!=0) { @set_time_limit(max($exec_time,1800)); }
			
			if(isset($_GET['action']) && $_GET['action']=='merge-it') {
				if(isset($_POST['zaba_merge_tax']) && $_POST['zaba_merge_tax']) {
					if(current_user_can('administrator')) {
						$duplicates=$_POST['zaba_merge_tax'];
						if($duplicates) {
							$posts=$this->find_posts($duplicates);
							$this->change_posts_terms($posts,$duplicates);
							$this->delete_terms($duplicates);
						}
					}
				}
			}
		}
	}
	
	private function find_terms($taxs) {
		foreach($taxs as $tax) {
			$terms[$tax]=get_terms($tax,array(
				'hide_empty'=>false,
				'orderby'=>'id',
				'order'=>'ASC',
			));
		}
		
		return $terms;
	}
	
	private function find_duplicates($taxonomies) {
		//NACITAJ VSETKY TERMS SO VSETKY TAX OKREM MENU,...
		$tax_terms=$this->find_terms($taxonomies);
		
		//ODDELENE PROCESY PRE KAZDU TAX ZVLAST
		$duplicates=null;
		foreach($tax_terms as $tax=>$terms) {
			//POROVNAJ KAZDY TERM S KAZDYM
			$tmp=array();
			foreach($terms as $termA) {
				foreach($terms as $termB) {
					//AK JE TERM-B NOVSI A SU Z ROVNAKEJ TAX A SU NA ROVNAKEJ UROVNI (HIERARCHICAL) A MAJU ZHODNE NAZVY
					if($termA->term_id<$termB->term_id && $termA->taxonomy==$termB->taxonomy && $termA->parent==$termB->parent && strtolower($termA->name)==strtolower($termB->name)) {
						//AK JE TERM-A DUPLICATE
						if(in_array($termA->term_id,$tmp)) {
							//NAJDI PRE KTORY ORIG
							foreach($duplicates as $orig=>$duplicate) {
								//AK SA NASIEL ORIG PRE TERM-A
								if(in_array($termA->term_id,$duplicate)) {
									//AK TENTO DUPLICATE ESTE NIE JE ZAPISANY
									if(!in_array($termB->term_id,$duplicates[$tax][$orig])) {
										$duplicates[$tax][$orig][]=$termB->term_id;
										$children=get_term_children($termB->term_id,$tax);
										if($children) {
											foreach($children as $child) {
												$duplicates[$tax][$termA->term_id][]=$child;
												$tmp[]=$child;
											}
										}
									}
								}
							}
						}
						//AK ESTE TERM-A NEEXISTUJE
						else {
							$duplicates[$tax][$termA->term_id][]=$termB->term_id;
							$children=get_term_children($termB->term_id,$tax);
							if($children) {
								foreach($children as $child) {
									$duplicates[$tax][$termA->term_id][]=$child;
									$tmp[]=$child;
								}
							}
							$tmp[]=$termB->term_id;
						}
					}
				}
			}
		}
		return $duplicates;
	}
	
	private function find_posts($tax_duplicates) {
		$tmp=array();
		foreach($tax_duplicates as $tax=>$duplicates) {
			foreach($duplicates as $orig=>$duplicate) {
				$tmp[$tax]['orig'][]=$orig;
				foreach($duplicate as $term_id) {
					$tmp[$tax]['dup'][]=$term_id;
				}
			}
		
			$tax_query=array('relation'=>'OR');
			$tax_query=array_merge($tax_query,array(array(
					'taxonomy'=>$tax,
					'field'=>'term_id',
					'terms'=>array_merge($tmp[$tax]['orig'],$tmp[$tax]['dup'])),
				));
			
			
			$args=array(
				'posts_per_page'=>-1,
				'post_type'=>'any',
				'fields'=>'ids',
				'tax_query'=>array(
					'relation'=>'OR',
					$tax_query,
				),
			);
			$posts[$tax]=new wp_query($args);
		}
		
		return $posts;
	}
	
	private function change_posts_terms($tax_posts,$duplicates) {
		foreach($tax_posts as $tax=>$posts) {
			foreach($posts->posts as $post_id) {
				$current_terms=wp_get_object_terms($post_id,$tax,array('fields'=>'ids'));
				$current_terms=array_flip($current_terms);
				
				foreach($duplicates[$tax] as $add=>$removes) {
					if(!isset($current_terms[$add])) {
						$current_terms[$add]=0;
					}
					
					foreach($removes as $remove) {
						if(isset($current_terms[$remove])) {
							unset($current_terms[$remove]);
						}
					}
				}
				$current_terms=array_flip($current_terms);
				
				//ULOZ DATA DO POSTU
				$save_it=wp_set_object_terms($post_id,$current_terms,$tax,false);
			}
		}
	}
	
	private function delete_terms($tax_terms=array()) {
		foreach($tax_terms as $tax=>$orig_terms) {
			foreach($orig_terms as $orig=>$terms) {
				foreach($terms as $term) {
					wp_delete_term($term,$tax);
				}
			}
		}
	}
}