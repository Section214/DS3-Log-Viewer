function QueryOp (url,params) {
    
    var urlParts = url.split("?");
    this.baseUrl = urlParts[0];
    var query = urlParts[1];

    this.url = url;
    this.incomingParams=[];
    this.existingParams = [];
    this.outputParams=[];
    
        
    if (query) {
        this.existingParams = this.getParams(query);
    }
    if(params!=null) {
        this.incomingParams = this.getParams(params);
    }
}
function QueryException(message) {
	this.Message = message;
}
QueryOp.prototype.getParams = function(inputs) {
        var params = [];
        if(typeof(inputs)=='string') {
            $.each(inputs.split("&"), function (index, value) {
				var valueParts = value.split("=");
				params[index] = [valueParts[0], decodeURIComponent(valueParts[1])];
			});
        }
        else {
            var i = 0;
            $.each(inputs, function (name, value) {
                params[i] = [name, value];
                i++;
            });
        }
        return params;
    }
QueryOp.prototype.mergeParams = function() {

    var op = this.outputParams;
    var ip = this.incomingParams;
    
    $.each(this.existingParams, function (index, value) {
			var newValIndex = false;
			$.each(ip, function (index2, value2) {
				if (newValIndex === false && value[0] == value2[0]) {
					newValIndex = index2;
				}
			});
			if (newValIndex === false) {
				op.push(value);
			}
			else {
				op.push(ip[newValIndex]);
				ip.splice(newValIndex, 1);
			}
		});
	
    	
    $.each(ip, function (ix, vl) {
			op.push(vl);
		});
	
	this.outputParams = op;
	this.incomingParams = ip;
	
}
QueryOp.prototype.getUrl = function () {

    var query="";
    
    $.each(this.outputParams, function (ix, vl) {
	   if (vl[1] != null) {
	       query += vl[0] + "=" + encodeURIComponent(vl[1]) + "&";
	   }
    });
    
    query = query.replace(/&$/, "");
    var url = this.baseUrl.replace(/\/$/,"") + "?" + query;
    return url.replace(/\?$/, "");
}

$.queryString = new Object();

$.extend($.queryString, {
	update: function (url, params) {	
		var queryOp = new QueryOp(url,params);
		queryOp.mergeParams();
		return queryOp.getUrl();
	},
    addOrUpdate: function(url, paramName, value) {
        if(paramName=="" || paramName==null) throw new QueryException('paramName is null');
        if(typeof(paramName)=='string'){
            if(value==undefined || value==null) throw 'value is null';
            return this.update(url,paramName+"="+value); 
        }     
    },
    delete: function(url,paramName) {
        
        if(paramName=="" || paramName==null) throw new QueryException('paramName is null');
        var queryOp = new QueryOp(url);
        queryOp.mergeParams();
        
        var exist = false;
        $.each(queryOp.outputParams,function(i,value) {
            if(value[0]==paramName) {
                queryOp.outputParams.splice(i,1);
                exist=true;
            }
        });
        if(!exist) { throw new QueryException('Parameter "'+paramName+'" does not exist');}
        return queryOp.getUrl();
    },
    getParam: function(url,paramName) {
        var result = null;
        if(paramName=="" || paramName==null) throw new QueryException('paramName is null');
        var queryOp = new QueryOp(url);
        queryOp.mergeParams();
        
        var exist = false;
        $.each(queryOp.outputParams,function(i,value) {
            if(value[0]==paramName) {
                result = value[1];
                exist=true;
            }
        });
        if(!exist) { throw new QueryException('Parameter "'+paramName+'" does not exist');}
        return result;
    }
});
