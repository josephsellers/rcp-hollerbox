<?php

function admin_check($post_id){
  if( current_user_can( 'administrator' ) ){
    $hide_in_admin = get_post_meta($post_id, 'rcp_hide_from_admin', true);
    if($hide_in_admin){
      return false;
    }else{
      return true;
    }
  }
  return true;
}
