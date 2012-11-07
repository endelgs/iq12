package eccen
{
	import mx.controls.Alert;
	import mx.utils.ObjectUtil;
	
	public class Request
	{
		protected var params:Object = new Object();
		public function Request()
		{
		
		}
		
		
		public static function toPHPArray(obj:Object,prepend:String = 'params'):Object
		{
			Alert.show(ObjectUtil.toString(obj)); 
			var request:Object = new Object();
			for(var i:String in obj)
			{
				var key:String = prepend+'['+i+']'; 
				if(!isNaN(obj[i]) || (obj[i] is String))
					request[key] = obj[i];
				else
					request[key] = toPHPArray(obj[i],key);
			}
			return request;
		}

	}
}