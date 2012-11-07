package eccen.graphs
{
	import mx.collections.ArrayCollection;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.http.mxml.HTTPService;
	
	
	public class HTTPUtil 
	{
		public var arrData:ArrayCollection = new ArrayCollection();
		
		public function HTTPUtil()
		{
			
		}

		public static function loadData(url:String, params:Object, success:Function = null, fault:Function = null):void
		{
		    var service:HTTPService = new HTTPService();
		    service.showBusyCursor = true;
	        if(success != null)
	        	service.addEventListener(ResultEvent.RESULT, success);
	        if(fault != null)
	        	service.addEventListener(FaultEvent.FAULT,fault);	        
		    service.url             = url;
		    service.method          = "POST";
		    service.resultFormat    = "object";
		    service.request         = params;
			service.send();
		}
		public function resultListener(event:ResultEvent):void
		{
			arrData = new ArrayCollection();
			
			if(event.result.response.metadata.affectedRows == 1)
				arrData.source.push(event.result.response.data.row);
			else if(event.result.response.metadata.affectedRows > 1)
				arrData = event.result.response.data.row as ArrayCollection;
		}
	}
	
}