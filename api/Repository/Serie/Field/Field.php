<?php
/*

class FieldRepository extends SerieRepository {
  $map = array(
    'sd_ivl_hdist', 
    'sd_ivl_avgdepth', 
    'sd_ivl_vdispers', 
    'sd_ivl_hmigr_hyp',
    'sd_ivl_vmigr_hyp',
    'sd_ivl_nrec',
    'sd_ivl_nfelt',
    'sd_ivl_etot',
    'sd_ivl_fmin',
    'sd_ivl_fmax',
    'sd_ivl_amin',
    'sd_ivl_amax'
  );

  public function get($option) {
    $ts1='sd_ivl_stime';
    $ts2='sd_ivl_stime';

    $te1='sd_ivl_etime';
    $te2='sd_ivl_etime';

    if($component=='nfelt') {
        $ts1='sd_ivl_felt_stime';
        $te1='sd_ivl_felt_etime';
    }
    
    if($component=='etot') {
        $ts1='sd_ivl_etot_stime';
        $te1='sd_ivl_etot_etime';
    }

    $que = "SELECT IFNULL(b.$ts1,b.$ts2), b.$field, IFNULL(b.sd_ivl_eqtype, 'Undefined') as eqtype, IFNULL(b.$te1,b.$te2) as etime, b.cc_id, b.cc_id2, b.cc_id3, b.cb_ids "
        . " FROM ss a, sd_ivl b"
        . " WHERE b.$field is not null and a.ss_code = '$code'"
        . " AND ((b.ss_id is not null and b.ss_id=a.ss_id) or (b.ss_id is null and b.sn_id is not null and b.sn_id=a.sn_id))"
        . " AND a.ss_pubdate <= now() and b.sd_ivl_pubdate <= now()"
        . " ORDER BY sd_ivl_etime";
  }
}
*/