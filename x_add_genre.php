<?php
/*
 * Copyright (c) 2018. YPY Global - All Rights Reserved.
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at.
 *
 *         http://ypyglobal.com/sourcecode/policy
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */
	include("includes/xradio_header.php");
	include("includes/xradio_function.php");
	include("includes/xradio_msg.php");

	$url_action="";
	if(isset($_GET['action'])){
		$action=$_GET['action'];
		if($action=="add"){
			if(isset($_POST['submit'])){
				if($_FILES['genre_img']['name']!=""){
					$genre_image="genre_".rand(0,99999)."_".$_FILES['genre_img']['name'];
					$tpath1='uploads/genres/'.$genre_image;
					$pic1=process_image($_FILES["genre_img"]["tmp_name"], $tpath1, 100);
				}
				$data = array(
					'name'  =>  $_POST['genre_name'],
					'img'  => $genre_image
				);
				$qry = insert_tbl('genres',$data);
				$_SESSION['msg']="6";
				header( "Location:x_add_genre.php?action=add");
				exit;
			}
		}
		elseif($action=="delete_image"){
			$genre_id=$_GET['id'];
			$img_res=mysqli_query($mysqli,"SELECT * FROM genres WHERE id=$genre_id");
			$img_res_row=mysqli_fetch_assoc($img_res);
			if($img_res_row['img']!=""){
				unlink('uploads/genres/'.$img_res_row['img']);
				$data = array(
					'img'  => ""
				);
				update_tbl('genres', $data, "WHERE id = $genre_id");
				header( "Location:x_add_genre.php?action=edit&id=".$genre_id);
				exit;
			}
		}
		elseif($action=="edit"){
			if(isset($_POST['submit']) and isset($_POST['genre_id'])){
				$genre_id=$_POST['genre_id'];
				$genre_name=$_POST['genre_name'];
				$genre_name=str_replace("'","\'",$genre_name);

				$genre_image="";
				$img_res=mysqli_query($mysqli,"SELECT * FROM genres WHERE id=$genre_id");
				$img_res_row=mysqli_fetch_assoc($img_res);
				if($img_res_row['img']!=""){
					$genre_image=$img_res_row['img'];
				}
				//check delete old image
				if($_FILES['genre_img']['name']!=""){
					if($genre_image!=""){
						unlink('uploads/genres/'.$genre_image);
					}
					//put new image
					$genre_image="genre_".rand(0,99999)."_".$_FILES['genre_img']['name'];
					$tpath1='uploads/genres/'.$genre_image;
					$pic1=process_image($_FILES["genre_img"]["tmp_name"], $tpath1, 100);
				}
				if($genre_image!=""){
					$data = array(
						'name'  =>  $genre_name,
						'img'  => $genre_image
					);
				}
				else{
					$data = array(
						'name'  =>  $genre_name,
						'img'  => ''
					);
				}
				update_tbl('genres', $data, "WHERE id = $genre_id");
				$_SESSION['msg']="7";
				header("Location:x_add_genre.php?action=edit&id=".$genre_id);
				exit;
			}
			$genre_id=$_GET['id'];
			$qry="SELECT * FROM genres where id=$genre_id";
			$result=mysqli_query($mysqli,$qry);
			$row=mysqli_fetch_assoc($result);
		}
		elseif($action=="copy"){
			$genre_id=$_GET['id'];
			$qry="SELECT * FROM genres where id=$genre_id";
			$result=mysqli_query($mysqli,$qry);
			$row=mysqli_fetch_assoc($result);
			$url_action="x_add_genre.php?action=add";
		}
	}
?>

<div class="row">
  <div class="col-md-12">
		<div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?php if(isset($_GET['id']) and $action=="edit"){?>Edit Genre<?php }else{?>Add Genre<?php }?></div>
        </div>
				<div class="divider"></div>
      </div>
      <div class="clearfix"></div>
      <div class="row card-top">
        <div class="col-md-12">
          <div class="col-md-12 col-sm-12">
            <?php if(isset($_SESSION['msg'])){?>
           	 <div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">??</span></button>
            	<?php echo $client_msg[$_SESSION['msg']] ; ?></a> </div>
            <?php unset($_SESSION['msg']);}?>
          </div>
        </div>
      </div>

			<!-- auto dismiss dialog -->
			<script type="text/javascript">
				$(document).ready(function () {
				window.setTimeout(function() {
    				$(".alert").fadeTo(500, 0).slideUp(500, function(){
        			$(this).remove();
    				});
				}, 800);
				});
			</script>

      <div class="card-home-body card-bottom">
        <form action="<?php echo $url_action ?>" name="addeditgenre" method="post" class="form form-horizontal" enctype="multipart/form-data">
        	<input type="hidden" name="genre_id" value="<?php echo $_GET['id'];?>" />
          <div class="section">
            <div class="section-body">
              <div class="form-group">
                <label class="col-md-3 control-label">Name (*)</label>
                <div class="col-md-6">
                  <input type="text" name="genre_name" id="genre_name" value="<?php if(isset($_GET['id']) || $action=="copy" ){echo $row['name'];}?>" class="form-control" required>
                </div>
              </div>

							<div class="form-group">
								<label class="col-md-3 control-label">Image (size 500x500)</label>
								<div class="col-md-6">
									<div class="fileupload_block">
										<input type="file" name="genre_img" value="fileupload" id="fileupload">
												<?php if(isset($_GET['id']) and $action=="edit" and $row['img']!="") {?>
												<div class="user_upload_img"><img type="image" src="uploads/genres/<?php echo $row['img'];?>" alt="genre image"/></div>
											<?php } else {?>
												<div class="user_upload_img"><img type="image" src="uploads/genres/genre_default.jpg" alt="genre image"/></div>
											<?php }?>
									</div>
								</div>
							</div>
							<?php if(isset($_GET['id']) and $row['img']!="" and $action=="edit") {?>
							<div class="form-group">
							 <div class="col-md-9 col-md-offset-3">
								 <div class="add_btn_accent"> <a href="x_add_genre.php?action=delete_image&id=<?php echo $_GET['id'];?>" onclick="return confirm('Do you want to delete this image?');">Delete image</a></div>
							 </div>
							</div>
							<?php }?>

							<div class="form-group">
								<div class="col-md-9 col-md-offset-3" style="margin-bottom:15px;margin-top:15px;color:#c375f2;">(*) Required Field.</div>
							</div>

              <div class="form-group">
                <div class="col-md-6 col-md-offset-3">
                  <button type="submit" name="submit" class="btn btn-primary">Save</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
		</div>
	</div>
</div>

<?php include("includes/xradio_footer.php");?>
