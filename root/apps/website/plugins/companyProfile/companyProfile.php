<?php
class companyProfile{
  const version='1.0.0';
  public function action(){
    /* globalize $website and $post */
    global $website,$post;
    /* check post url */
    if($post->url!='company-profile'){
      return false;
    }
    /* check get request file */
    if(!isset($_GET['file'])){
      return false;
    }
    /* set pdf file */
    $pdfFile=EDAY_INDEX_DIR.'files/company_profile_hci_v2.pdf';
    /* request download */
    if($_GET['file']=='download'){
      $filename='company-profile-of-pt-human-capital-international.pdf';
      header('Content-Type: application/pdf');
      header('Content-Disposition: attachment; filename="'.$filename.'"');
      readfile($pdfFile);
      exit;
    }
    /* request view */
    if($_GET['file']=='view'){
      header('Content-Type: application/pdf');
      readfile($pdfFile);
      exit;
    }return true;
  }
}


