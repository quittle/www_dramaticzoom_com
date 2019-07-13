<?php
	require_once "email.php";
	function sendRegistration($user, $email, $special){
		$subject = "Welcome to Dramatic Zoom!";
		
		$link = 'http://drzm.co/verify?u=' . $user . '&e=' . $email . '&s=' . $special;

		$body = '<html>
		<body marginWidth="0" marginHeight="0">
		<table style="border-collapse:collapse"><tr><td width="100%" height="100%" marginWidth="0" marginHeight="0" style="background-color:#b0ccd9; font-family: Arial, Helvetica, sans-serif; font-size:16px; line-height:20px; padding:0 25px 25px 25px;" bgcolor="#b0ccd9">
			<a style="color:#212121 !important" href="http://drzm.co">
				<table width="100%" style="border-collapse:collapse"><tr><td style="padding:0">
				<div style="border-bottom:2px solid #96adbe;border-radius:0 0 10px 10px">
				<div style="border-bottom:3px solid #7b8ea2;border-radius:0 0 10px 10px">
					<table cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse"><tr><td style="background-color:#fff; border-radius:0 0 10px 10px; font-weight:bold; padding:18px; font-size:26px; text-align:center" bgcolor="#fff">
						<a style="color:#212121 !important; text-decoration:none !important" href="http://drzm.co"><font color="#212121" style="text-decoration:none !important; font-family: Arial, Helvetica, sans-serif; line-height:30px">WELCOME TO DRAMATIC ZOOM!</font></a>
					</td></tr></table>
				</div></div>
				</td></tr></table>
			</a>
			<table width="100%" style="padding:16px 0"><tr><td width="100%"><p marginWidth="0" marginHeight="0" style="margin:0 !important;font-size:20px;line-height:30px"><font style="font-family: Arial, Helvetica, sans-serif">Congratulations on joining a budding community of Zoomographers! You\'re now ready to start sharing photos in a revolutionary, new way. The days of photo editing with arrows, text, and circles that obstruct the image are long gone. Dramatic Zoom allows you to share the original image in full before zooming in on what you want your friends to see. Get started sharing your very own Zoomographs today!</font></p></td></tr></table>
			<div style="padding: 25px 0 10px 0;">
				<table style="border-collapse:collapse"><tr><td>
					<a style="color:#212121 !important" href="http://drzm.co/manage">
						<div style="border-bottom:2px solid #96adbe; border-radius:10px">
						<div style="border-bottom:3px solid #7b8ea2; border-radius:10px">
							<table style="border-collapse:collapse"><tr><td height="19px" style="background-color: #fff; font-size: 19px; font-weight: bold; padding: 15px; border-radius:10px" bgcolor="#fff">
								<a style="color:#212121 !important; text-decoration:none !important" href="http://drzm.co/manage"><font color="#212121" style="text-decoration:none !important;font-family: Arial, Helvetica, sans-serif">YOUR NEW ACCOUNT</font></a>
							</td></tr></table>
						</div></div>
					</a>
				</td></tr></table>
			</div>

			<table width="100%" style="padding:16px 0"><tr><td width="100%"><p marginWidth="0" marginHeight="0" style="margin:0 !important;font-size:20px;line-height:30px"><font style="font-family: Arial, Helvetica, sans-serif">With your new account you can view and manage your zoomographs (the images you upload that zoom).  New features are in the works that will let you share and view other\'s zoomographs.</font></p></td></tr></table>
			<hr width="90%" style="background-color: #7b8ea2; color: #7b8ea2; border: 1px solid #7b8ea2;" />
			<table width="100%" style="padding:16px 0"><tr><td width="100%"><p marginWidth="0" marginHeight="0" style="margin:0 !important;font-size:20px;line-height:30px"><font style="font-family: Arial, Helvetica, sans-serif">You have registered a new account with the username: ' . $user . '</font></p></td></tr></table>
			<table width="100%" style="padding:16px 0"><tr><td width="100%"><p marginWidth="0" marginHeight="0" style="margin:0 !important;font-size:20px;line-height:30px"><font style="font-family: Arial, Helvetica, sans-serif">Click the following link to confirm your email address. If you believe you received this email in error, just delete it and the account will be automatically deleted in the next 48 hours.</font></p></td></tr></table>
			<table width="100%" style="padding:16px 0"><tr><td width="100%"><p marginWidth="0" marginHeight="0" style="margin:0 !important;font-size:20px;line-height:30px">
			<table width="100%" style="border-radius:15px; border:3px solid #7b8ea2; box-shadow:0 0 17px 0 rgba(0,0,0,.4) !important; -webkit-box-shadow:0 0 17px 0 rgba(0,0,0,.4) !important; -moz-box-shadow:0 0 17px 0 rgba(0,0,0,.4) !important; -o-box-shadow:0 0 17px 0 rgba(0,0,0,.4) !important" bgcolor="#a7d7f7"><tr><td>
				<a href="' . $link . '" target="_new">
					<div style="border-collapse:collapse; border-radius:15px">
						<table width="100%" style="border-collapse:collapse"><tr><td width="100%" style="background-color:#a7d7f7; text-align:center; border-radius:12px; padding: 20px 50px;" align="center" bgcolor="#a7d7f7">
							<a href="' . $link . '" target="_new"><font style="font-family: Arial, Helvetica, sans-serif">Click here to complete registration</font></a>
						</td></tr></table>
					</div>
				</a>
			</td></tr></table>
			</p></td></tr></table>
			<br />
			<table width="100%" style="padding:16px 0"><tr><td width="100%"><p marginWidth="0" marginHeight="0" style="margin:0 !important;font-size:20px;line-height:30px"><font style="font-family: Arial, Helvetica, sans-serif">
				Sincerely Yours,<br />
				The Dramatic Zoom Team
			</font></p></td></tr></table>
		</td></tr></table>
		</body></html>';
		
		sendEmail($email, $subject, $body, true);
	}
?>