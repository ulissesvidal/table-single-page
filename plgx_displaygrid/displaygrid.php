<?php
#-------------------------------------------------------------------------------
# Copyright (c) 2009 Gomilsek-informatika.
# All rights reserved. This program and the accompanying materials
# are made available under the terms of the GNU Public License v2.0
# which accompanies this distribution, and is available at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# 
# Contributors:
# 	Gomilsek-informatika  (initial API and implementation)
# Contact:
# 	customers@toolsjx.com
#-------------------------------------------------------------------------------
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
require_once(JPATH_ROOT.'/components/com_grid/GridBuilder.php');


class plgContentDisplaygrid extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	
	 function onContentPrepare($context, &$row, &$params, $page = 0)
        {
        		
             // A database connection is created
             //$db = JFactory::getDBO();
             // simple performance check to determine whether bot should process further
             // echo("AAAAAA");
             if ( JString::strpos( $row->text, 'insertgrid' ) === false ) {
             	
                        return true;
             }
             // echo("BBBBBBBB");
	                // expression to search for
                $regex = '/{insertgrid\s*.*?}/i';
 
                // check whether plugin has been unpublished
                if ( !$this->params->get( 'enabled', 1 ) ) {
                        $row->text = preg_replace( $regex, '', $row->text );
                        return true;
                }
                
                $document = JFactory::getDocument();
				$document->addScript(JUri::root().'components/com_grid/js/ajax_queue.js' );
				$document->addScript( JUri::root().'components/com_grid/js/grid.js' );
                $document->addStyleSheet(JUri::root().'components/com_grid/css/grid.css');
				// find all instances of plugin and put in $matches
                preg_match_all( $regex, $row->text, $matches );
 
                // Number of plugins
                $count = count( $matches[0] );
 
                // plugin only processes if there are any instances of the plugin in the text
                if ( $count ) {
                        // Get plugin parameters
                        $style = $this->params->def( 'style', -2 );
                        $this->_process( $row, $matches, $count, $regex, $style );
                }
                // No return value
        }
// The proccessing function
        function _process( &$row, &$matches, $count, $regex, $style )
        {
                for ( $i=0; $i < $count; $i++ )
                {
                        $load = str_replace( 'insertgrid', '', $matches[0][$i] );
                        $load = str_replace( 'ID', '', $load );
                        $load = str_replace( '=', '', $load );
                        $load = str_replace( '{', '', $load );
                        $load = str_replace( '}', '', $load );
                        $load = trim( $load );
 											
                        $grid = $this->_load( $load.'_'.$i, $row);
                        //echo 'aaaaaaaaaaaaaaaaaa'.$grid;
                        //echo $matches[0][$i];
                        $row->text = preg_replace( '{'. $matches[0][$i] .'}', $grid, $row->text );
                }
 
                // removes tags without matching module positions
                $row->text = preg_replace( $regex, '', $row->text );
        }
// The function who takes care for the 'completing' of the plugins' actions : loading the module(s)
        function _load( $id, &$row)
        {
        	$session = JFactory::getSession();
        	$session->set($id, $row);
        	$config = new GridConfigManager($id);
        	if(!$config->error){
			    switch($config->typejx){
					case 'grid':{
	                                  if(class_exists('GridView')) $builder = new GridView($id, $config);
	                                  else die("Please install TableJX");
	                                } break;
					case 'card':{
	                                  if(class_exists('CardView')) $builder = new CardView($id, $config);
	                                  else die("Please install CardJX");
	                                } break;
					case 'grap':{
	                                  if(class_exists('GraphView')) $builder = new GraphView($id, $config);
	                                  else die("Please install GraphJX");
	                                } break;
	                                case 'reco':{
	                                  if(class_exists('RecordView')) $builder = new RecordView($id, $config);
	                                  else die("Please install RecordJX");
	                                }
				}
	           return $builder->build();
        	}
        	else return $config->error;
        }
     
        
}
             
	

