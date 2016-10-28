# WOVODAT
- ERRORS:
- - UBO (Total Seismic Energy):
- - - Reason: In es_sd_ivl table, the value of 'sd_ivl_etot' column is " Total Seismic Energy" (space at the beginning) not "Total Seismic Energy" (no space at the beginning)
- - - Solution: Trim those strings
- - UBO (RSAM Count) and CAB(RSAM Count):
- - - Reason: there is no column sd_rsm_code in getting data query "select a.sd_rsm_code as data_code ... from sd_rsm as a, ....."
- - - Solution: Need to clarify.
- - B201 (Strain Component 1)
- - - Reason: syntax error in queries
- - - Solution: fix it
- -  "StHelens_COSPEC_airplane
- - - Reason: syntax error in queries
- - - Solution: fix it

