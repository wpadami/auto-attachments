<?php
/**
 * Classic-editor "Insert Auto Attachments Shortcode" dialog.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Adds a media-button that opens a dialog (the native HTML `<dialog>`
 * element - no jQuery or jQuery UI involved) for building
 * [imageaa]/[filesaa]/[musicaa]/[videoaa] shortcodes from a post's
 * existing attachments, and for excluding those same attachments from the
 * automatic listing (via ShortcodePanelAjax) so they aren't shown twice.
 */
class ShortcodePanel {

	/**
	 * Used to populate the panel's selectboxes.
	 *
	 * @var AttachmentRepository
	 */
	private $repository;

	/**
	 * Constructor.
	 *
	 * @param AttachmentRepository $repository Used to populate the panel's selectboxes.
	 */
	public function __construct( AttachmentRepository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * Hook the button, dialog, and its inline script.
	 */
	public function register_hooks(): void {
		add_action( 'media_buttons', array( $this, 'render_button' ), 11 );
		add_action( 'admin_footer', array( $this, 'render_dialog' ) );
		add_action( 'admin_head-post-new.php', array( $this, 'render_script' ) );
		add_action( 'admin_head-post.php', array( $this, 'render_script' ) );
		add_action( 'admin_head-edit.php', array( $this, 'render_script' ) );
	}

	/**
	 * Render the toolbar button that opens the dialog.
	 */
	public function render_button(): void {
		$icons_url = plugins_url( '/auto-attachments/includes' );
		?>
		<a id="auto_attachments_sh_button" title="<?php esc_attr_e( 'Auto Attachments Shortcodes', 'autoa' ); ?>" class="button-secondary" href="#" style="display:inline-block;padding:10px;vertical-align:middle;cursor:pointer;">
			<img src="<?php echo esc_url( $icons_url . '/images/aamenu.png' ); ?>" alt="<?php esc_attr_e( 'Auto Attachments Shortcodes', 'autoa' ); ?>" style="vertical-align:middle;" />
		</a>
		<?php
	}

	/**
	 * Render the (initially hidden) dialog markup.
	 */
	public function render_dialog(): void {
		$post = get_post();
		if ( ! $post instanceof \WP_Post ) {
			return;
		}

		$icons_url = plugins_url( '/auto-attachments/includes' );
		?>
		<style>
			#auto_attachments_sh_window .lnginpt input[type=text] { width: 175px; }
			#auto_attachments_sh_window select { width: 110px; max-width: 110px; }
		</style>
		<dialog id="auto_attachments_sh_window" aria-label="<?php esc_attr_e( 'Auto Attachments Shortcodes', 'autoa' ); ?>" style="position:relative;width:700px;max-width:90vw;max-height:85vh;overflow:auto;padding:20px;">
			<button type="button" id="auto_attachments_sh_close" class="button" aria-label="<?php esc_attr_e( 'Close', 'autoa' ); ?>" style="position:absolute;top:10px;right:10px;">&times;</button>
			<div style="background:url(<?php echo esc_url( $icons_url . '/images/32x32aa.png' ); ?>) no-repeat;" class="icon32"></div>
			<h2><?php esc_html_e( 'Create New Auto Attachments Shortcode', 'autoa' ); ?></h2>
			<h4>
				<?php esc_html_e( 'Files Already Loaded', 'autoa' ); ?>
				<img class="spinneri" src="<?php echo esc_url( $icons_url . '/images/spinner.gif' ); ?>" style="display:none;" />
				<small><?php esc_html_e( "If you don't see any file name and id press label of selectbox", 'autoa' ); ?></small>
			</h4>

			<label for="image"><span id="resgetir" tur="image" style="cursor:pointer;font-weight:bold;"><?php esc_html_e( 'Image', 'autoa' ); ?></span></label>
			<?php $this->render_select( 'simage', $this->repository->images( $post->ID ), __( 'No Image', 'autoa' ) ); ?>

			<label for="audio"><span id="audgetir" tur="audio" style="cursor:pointer;font-weight:bold;"><?php esc_html_e( 'Audio', 'autoa' ); ?></span></label>
			<?php $this->render_select( 'saudio', $this->repository->audio( $post->ID ), __( 'No Audio', 'autoa' ) ); ?>

			<label for="video"><span id="vidgetir" tur="video" style="cursor:pointer;font-weight:bold;"><?php esc_html_e( 'Video', 'autoa' ); ?></span></label>
			<?php $this->render_select( 'svideo', $this->repository->video( $post->ID ), __( 'No Video', 'autoa' ) ); ?>

			<label for="idselect"><span id="filegetir" tur="application" style="cursor:pointer;font-weight:bold;"><?php esc_html_e( 'File', 'autoa' ); ?></span></label>
			<?php $this->render_select( 'sfile', $this->repository->files( $post->ID ), __( 'No File', 'autoa' ) ); ?>

			<div class="clear"></div>
			<table class="widefat fixed">
				<thead>
					<tr>
						<th width="90"><?php esc_html_e( 'Shortcode For', 'autoa' ); ?></th>
						<th width="170"><?php esc_html_e( 'Item Id(s)', 'autoa' ); ?></th>
						<th width="60"></th>
						<th width="230"><?php esc_html_e( 'Description', 'autoa' ); ?></th>
					</tr>
				</thead>
				<tbody class="lnginpt">
					<?php
					$this->render_row( 'resids', 'ex_rsm', 'resim', 'rsmadd', $post, __( 'Image(s)', 'autoa' ), __( 'Enter the <strong>Ids</strong> (comma seperated) of image(s) here. <strong>Image selectbox</strong> shows which files loaded in this post.', 'autoa' ) );
					$this->render_row( 'dosyids', 'ex_dosya', 'dosya', 'dosyadd', $post, __( 'File(s)', 'autoa' ), __( 'Enter the Ids (comma seperated) of file(s) here. <strong>File selectbox</strong> shows which files loaded in this post.', 'autoa' ) );
					$this->render_row( 'muzids', 'ex_muz', 'muzik', 'muzadd', $post, __( 'Audio(s)', 'autoa' ), __( 'Enter the Ids (comma seperated) of aufido file(s) here. <strong>Audio selectbox</strong> shows which files loaded in this post.', 'autoa' ) );
					$this->render_row( 'vidids', 'ex_vid', 'video', 'vidadd', $post, __( 'Video(s)', 'autoa' ), __( 'Enter the Ids (comma seperated) of video file(s) here. <strong>Video selectbox</strong> shows which files loaded in this post.', 'autoa' ) );
					?>
				</tbody>
			</table>
			<p class="dee"></p>
			<h3><?php esc_html_e( 'Usage', 'autoa' ); ?></h3>
			<table class="widefat fixed">
				<thead>
					<tr>
						<th width="100px;"><?php esc_html_e( 'Situation', 'autoa' ); ?></th><th><?php esc_html_e( 'Description', 'autoa' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php esc_html_e( 'Create', 'autoa' ); ?></td><td><?php echo wp_kses_post( __( 'For create any shortcode, please add ids with comma to text areas. When you press <strong>Create</strong> button, plugin will create a shortcode and an exclude code for show files properly.', 'autoa' ) ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Clear', 'autoa' ); ?></td><td><?php echo wp_kses_post( __( 'For clear, Wipe text area and press <strong>Create</strong> button. This will clear exclude code from your post but you will still have shortcode. You can delete shortcode from your content. Shortcode will show your items properly, but this may duplicate files (in after content area).', 'autoa' ) ); ?></td>
					</tr>
				</tbody>
			</table>
		</dialog>
		<?php
	}

	/**
	 * Render a selectbox of attachments (or an "empty" placeholder option).
	 *
	 * @param string     $id          Selectbox element ID.
	 * @param \WP_Post[] $attachments Attachments to list.
	 * @param string     $empty_label Label shown when there are none.
	 */
	private function render_select( string $id, array $attachments, string $empty_label ): void {
		echo '<select id="' . esc_attr( $id ) . '">';
		if ( empty( $attachments ) ) {
			echo '<option id="none">' . esc_html( $empty_label ) . '</option>';
		} else {
			foreach ( $attachments as $attachment ) {
				$label = '(' . $attachment->ID . ') ' . $attachment->post_name;
				echo '<option id="' . esc_attr( $attachment->ID ) . '" title="' . esc_attr( $label ) . '">' . esc_html( $label ) . '</option>';
			}
		}
		echo '</select>';
	}

	/**
	 * Render one "Item Id(s)" table row.
	 *
	 * @param string   $input_id    Text input element ID.
	 * @param string   $meta_key    Post meta key holding the current exclude value.
	 * @param string   $durum       "durum" (kind) value sent to ShortcodePanelAjax.
	 * @param string   $button_id   "Create" button element ID.
	 * @param \WP_Post $post        Current post.
	 * @param string   $label       Row label (already translated).
	 * @param string   $description Row description (already translated, may contain basic HTML).
	 */
	private function render_row( string $input_id, string $meta_key, string $durum, string $button_id, \WP_Post $post, string $label, string $description ): void {
		?>
		<tr>
			<td><?php echo esc_html( $label ); ?></td>
			<td><input id="<?php echo esc_attr( $input_id ); ?>" type="text" value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>" name="<?php echo esc_attr( $post->ID ); ?>" durum="<?php echo esc_attr( $durum ); ?>" /></td>
			<td><a class="button-primary" id="<?php echo esc_attr( $button_id ); ?>" href="#" title="<?php esc_attr_e( 'Create', 'autoa' ); ?>" style="color:#FFF;"><span><?php esc_html_e( 'Create', 'autoa' ); ?></span></a></td>
			<td><small><?php echo wp_kses_post( $description ); ?></small></td>
		</tr>
		<?php
	}

	/**
	 * Print the dialog's inline script - plain JS, no jQuery or jQuery UI
	 * (the dialog is the native `<dialog>` element). $post_id and the
	 * nonce are the only dynamic values, both server-generated and safe
	 * in this single-quoted JS string context.
	 */
	public function render_script(): void {
		$post   = get_post();
		$postid = $post instanceof \WP_Post ? $post->ID : 0;
		$nonce  = wp_create_nonce( ShortcodePanelAjax::NONCE_ACTION );
		?>
		<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function () {
			var POST_ID = <?php echo (int) $postid; ?>;
			var NONCE = '<?php echo esc_js( $nonce ); ?>';

			var dialog = document.getElementById('auto_attachments_sh_window');
			var openButton = document.getElementById('auto_attachments_sh_button');
			var closeButton = document.getElementById('auto_attachments_sh_close');

			if (!dialog || typeof dialog.showModal !== 'function') {
				return;
			}

			if (openButton) {
				openButton.addEventListener('click', function (event) {
					event.preventDefault();
					dialog.showModal();
				});
			}
			if (closeButton) {
				closeButton.addEventListener('click', function (event) {
					event.preventDefault();
					dialog.close();
				});
			}

			function setSpinnerVisible(visible) {
				var spinner = dialog.querySelector('.spinneri');
				if (spinner) {
					spinner.style.display = visible ? '' : 'none';
				}
			}

			function loadSelect(triggerId, targetId) {
				var trigger = document.getElementById(triggerId);
				var target = document.getElementById(targetId);
				if (!trigger || !target) {
					return;
				}
				trigger.addEventListener('click', function () {
					setSpinnerVisible(true);
					var params = new URLSearchParams({
						action: 'get_imgs',
						post_id: String(POST_ID),
						postmim: trigger.getAttribute('tur') || '',
						nonce: NONCE
					});
					fetch(ajaxurl + '?' + params.toString())
						.then(function (response) { return response.json(); })
						.then(function (items) {
							setSpinnerVisible(false);
							target.innerHTML = '';
							items.forEach(function (item) {
								var option = document.createElement('option');
								var label = '(' + item.id + ') ' + item.post_name;
								option.value = item.id;
								option.title = label;
								option.textContent = label;
								target.appendChild(option);
							});
						})
						.catch(function () { setSpinnerVisible(false); });
				});
			}
			loadSelect('resgetir', 'simage');
			loadSelect('audgetir', 'saudio');
			loadSelect('vidgetir', 'svideo');
			loadSelect('filegetir', 'sfile');

			function createShortcode(buttonId, inputId, tag) {
				var button = document.getElementById(buttonId);
				var input = document.getElementById(inputId);
				if (!button || !input) {
					return;
				}
				button.addEventListener('click', function () {
					setSpinnerVisible(true);
					var ids = input.value;
					if (ids !== '' && typeof window.send_to_editor === 'function') {
						window.send_to_editor('[' + tag + ' id=' + ids + ']');
					}
					var body = new URLSearchParams({
						action: 'ex_aa',
						durum: input.getAttribute('durum') || '',
						post_id: input.getAttribute('name') || '',
						post_meta: ids,
						nonce: NONCE
					});
					fetch(ajaxurl, {
						method: 'POST',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
						body: body.toString()
					}).then(function () {
						setSpinnerVisible(false);
						dialog.close();
					}).catch(function () { setSpinnerVisible(false); });
				});
			}
			createShortcode('rsmadd', 'resids', 'imageaa');
			createShortcode('muzadd', 'muzids', 'musicaa');
			createShortcode('vidadd', 'vidids', 'videoaa');
			createShortcode('dosyadd', 'dosyids', 'filesaa');
		});
		</script>
		<?php
	}
}
