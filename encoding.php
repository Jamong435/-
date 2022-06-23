

<?php   
//에러 로그 확인 * 0 hide * 1 show
ini_set( 'display_errors', '0' );


//원본 다운로드 폴더
$dir = "/var/www/html/uploadfile/file/"; 
//업로드 지정 폴더
$uploaddir="/var/www/html/uploadfile/uploaded/";

//다운로드 폴더 선택
if (is_dir($dir)){
	//다운로드 폴더 열기
  if ($dh = opendir($dir)){
	  // 다운로드 폴더 읽기
    while (($file = readdir($dh)) !== false ){
			//앞에 상위폴더 나타내는거 빼기.  
		  if($file == '.' ||  $file =='..' || $file == '.mp4'){
			continue;
			}

		// 파일 정보 읽어 들이기
		$output= shell_exec('/usr/bin/ffprobe -show_streams '.$dir.$file.' 2>&1;');
		// 정보 데이터 가져오기
		$without_extension = substr($file, 0, strrpos($file, ".")); 
		//원본 확장자 삭제	ex/ test.mp4 --> test
	

		$alldata = substr( $output, strpos($output,"[STREAM]"), -1);
		//[STREAM] 아래로 세부 정보들을 가져온다.
		$a=substr( $alldata, strpos($alldata,"index=0"), strpos($alldata,"[/STREAM]"));
		//video값
		$b=substr( $alldata, strpos($alldata,"index=1"), -1);
		//audio값
		preg_match_all('/(.*)=(.*)/i', $a, $matches);
		$video_info = [];
		if(!empty($matches)) {
				foreach($matches[1] as $k => $v) {
				$video_info[$v] = $matches[2][$k];
				}
			}
		//video값 배열값에 넣기
		preg_match_all('/(.*)=(.*)/i', $b, $matches2);
		$video_info2 = [];
		if(!empty($matches2)) {
			foreach($matches2[1] as $k => $v) {
			$video_info2[$v] = $matches2[2][$k];
				}
			}
		//audio값 배열에 넣기


		$forwhat = $matches[2][4];
		$forwhat2 = $matches2[2][4];

		if($forwhat == ""){
			echo $file."<--null값으로 넘어온것";
			echo "<br>";
		}
	
		print_r($forwhat);

		if($forwhat == "video"){
			
			//인코딩 코드
			shell_exec('/usr/bin/ffmpeg -i '.$dir.$file.' -an '.$uploaddir.$without_extension.'.mp4 2>&1;');
			
			// 썸내일 코드
			shell_exec('/usr/bin/ffmpeg -i '.$dir.$file.' -pix_fmt rgb24 -vframes 1 -ss 00:00:05 -s 350x250  '.$uploaddir.$without_extension.'.jpg 2>&1');

			echo $without_extension."<-- 업로드성공 ";
			echo "<br>";

		}else{
			echo $without_extension."<-- 이파일은 video값이 아닙니다";
			echo "<br>";
		}

		//print_r($forwhat);

	
		//shell_exec('/usr/bin/ffmpeg -i '.$dir.$file.' -an '.$uploaddir.$uploadfile.'.flv 2>&1;');
		//인코딩 해서 보내기.
						
			//echo $without_extension;		
	}                                           
    closedir($dh);                              
  }
 
}      




?>  


