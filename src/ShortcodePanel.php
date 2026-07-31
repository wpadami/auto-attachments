<?php
/**
 * Classic-editor "Insert Auto Attachments Shortcode" dialog.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Adds a media-button that opens a jQuery UI dialog for building
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
		<a id="auto_attachments_sh_button" title="<?php esc_attr_e( 'Auto Attachments Shortcodes', 'autoa' ); ?>" class="button-secondary" href="#" style="cursor:pointer;">
			<img src="<?php echo esc_url( $icons_url . '/images/aamenu.png' ); ?>" alt="<?php esc_attr_e( 'Auto Attachments Shortcodes', 'autoa' ); ?>" style="margin-top:-2px;" />
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
		<div id="auto_attachments_sh_window" title="<?php esc_attr_e( 'Auto Attachments Shortcodes', 'autoa' ); ?>" style="display: none;">
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
		</div>
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
				echo '<option id="' . esc_attr( $attachment->ID ) . '">' . esc_html( $attachment->post_name ) . '(' . esc_html( (string) $attachment->ID ) . ')</option>';
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
	 * Print the dialog's inline script. $post_id and the nonce are the
	 * only dynamic values, both server-generated and safe in this
	 * single-quoted JS string context.
	 */
	public function render_script(): void {
		$post   = get_post();
		$postid = $post instanceof \WP_Post ? $post->ID : 0;
		$nonce  = wp_create_nonce( ShortcodePanelAjax::NONCE_ACTION );
		?>
		<script type="text/javascript">
		jQuery(function ($) {
			$('.lnginpt input[type=text]').css('width','175px');
			$('#auto_attachments_sh_window select').css('width','110px','max-width','110px');

			$('#auto_attachments_sh_window').dialog({
				autoOpen: false,
				width: '700',
				height: '600',
				modal: true,
				draggable: false,
				resizable: false,
				closeOnEscape: true
			});

			$('#auto_attachments_sh_button').click(function () {
				$('#auto_attachments_sh_window').dialog('open');
			});

			function aaLoadSelect(triggerId, targetId) {
				$(triggerId).on('click', function () {
					$('.spinneri').show();
					var data = {
						action: 'get_imgs',
						post_id: '<?php echo (int) $postid; ?>',
						postmim: $(this).attr('tur'),
						nonce: '<?php echo esc_js( $nonce ); ?>'
					};
					$.getJSON(ajaxurl, data, function (response) {
						$('.spinneri').hide();
						var cb = '';
						$.each(response, function (i, tata) {
							cb += '<option value="' + tata.id + '">' + tata.post_name + '(' + tata.id + ')</option>';
						});
						$(targetId + ' option').remove();
						$(targetId).append(cb);
					});
				});
			}
			aaLoadSelect('#resgetir', '#simage');
			aaLoadSelect('#audgetir', '#saudio');
			aaLoadSelect('#vidgetir', '#svideo');
			aaLoadSelect('#filegetir', '#sfile');

			function aaCreateShortcode(buttonId, inputId, tag) {
				$(buttonId).on('click', function () {
					$('.spinneri').show();
					var ids = $(inputId).val();
					if (ids !== '') {
						send_to_editor('[' + tag + ' id=' + ids + ']');
					}
					var data = {
						action: 'ex_aa',
						durum: $(inputId).attr('durum'),
						post_id: $(inputId).attr('name'),
						post_meta: ids,
						nonce: '<?php echo esc_js( $nonce ); ?>'
					};
					$.post(ajaxurl, data, function () {
						$('.spinneri').hide();
						$('#auto_attachments_sh_window').dialog('close');
					});
				});
			}
			aaCreateShortcode('#rsmadd', '#resids', 'imageaa');
			aaCreateShortcode('#muzadd', '#muzids', 'musicaa');
			aaCreateShortcode('#vidadd', '#vidids', 'videoaa');
			aaCreateShortcode('#dosyadd', '#dosyids', 'filesaa');
		});
		</script>
		<?php
	}
}
