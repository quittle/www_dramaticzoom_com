deleteZoomograph=(function(orig){return function(){if(confirm("Are sure you want to delete this zoomograph? This can't be undone")){return orig.apply(deleteZoomograph,arguments)}}})(deleteZoomograph);