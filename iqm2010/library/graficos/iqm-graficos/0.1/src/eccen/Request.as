package eccen
{
	public class Request
	{
		protected var params:Object = new Object();
		public function Request()
		{
		
		}
		
		public function get params()
		{
			var request:Object = new Object();
			
			for(var prop:String in params)
			{
				request['params['++']'];
			}
			
			return request;
		}
		public function set params(value:Object):void
		{
			params = new Request();
			params.params = value;
		}

	}
}