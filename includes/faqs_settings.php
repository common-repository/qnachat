<?php 

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="qnac-faqs">
    <div class="inner-content">
    <?php 
    $faqs = get_option('qnac_faq_sets'); 
    foreach ($faqs as $tag=>$values) {
        ?>
        <div class="tag done" value="<?php echo esc_attr($tag); ?>">
            <div class="tag-set done">
                <h5><img width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/tag.png'; ?>"><span><?php echo esc_attr($tag); ?></span></h5>
                <input class="t-field" type="text" value="<?php echo esc_attr($tag); ?>">
                <div class="actions">
                    <img class="t delete" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/cross.svg'; ?>">
                    <img class="t edit" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/edit.svg'; ?>">
                    <img class="t confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                </div>
            </div>
        <?php        
        foreach ($values as $q => $a) {
        ?>
                <div class="qa done">    
                    <div class="question done" value="<?php echo esc_html($q); ?>">
                        <p class="label"><?php echo esc_attr($q); ?></p>
                        <input class="q-field" type="text" value="<?php echo esc_html($q); ?>">
                        <div class="actions">
                            <img class="q delete" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/cross.svg'; ?>">
                            <img class="q confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                            <img class="q edit" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/edit.svg'; ?>">
                        </div>
                    </div>
                    <div class="answer done" value="<?php echo esc_html($a); ?>">
                        <p class="label"><?php echo esc_attr($a); ?></p>
                        <input class="a-field" type="text" value="<?php echo esc_html($a); ?>">
                        <div class="actions">
                            <img class="a confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                            <img class="a edit" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/edit.svg'; ?>">
                        </div>
                    </div>
                </div>    
                <?php
                }
                ?>
            <div class="qa waiting">
                <div class="question add-new">
                    <p class="label"><?php esc_html_e('Add a new question', 'qnachat'); ?></p>
                    <input class="q-field" type="text">
                    <div class="actions">
                        <img class="q delete" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/cross.svg';?>">
                        <img class="q confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="q edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div> 
                <div class="answer add-new cg-hide">
                    <p class="label"><?php esc_html_e("The question's answer.", 'qnachat'); ?></p>
                    <input class="a-field" type="text">                    
                    <div class="actions">
                        <img class="a confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="a edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div>                 
            </div>
            <div class="qa cg-hide">
                <div class="question add-new">
                    <p class="label"><?php esc_html_e('Add a new question', 'qnachat'); ?></p>
                    <input class="q-field" type="text">  
                    <div class="actions">
                        <img class="q delete" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/cross.svg';?>">
                        <img class="q confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="q edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div> 
                <div class="answer add-new cg-hide">
                    <p class="label"><?php esc_html_e("The question's answer.", 'qnachat'); ?></p>
                    <input class="a-field" type="text">  
                    <div class="actions">
                        <img class="a confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="a edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div>                 
            </div> 
        </div>
        <?php
}

        ?>
        <div class="tag waiting">
            <div class="tag-set waiting">
                <h5><img width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/tag.png';?>"><span>Add a tag</span></h5>
                <input class="t-field" type="text">
                <div class="actions">
                    <img class="t delete" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/cross.svg';?>">
                    <img class="t edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    <img class="t confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                </div>
            </div>            
            <div class="qa waiting">
                <div class="question add-new">
                    <p class="label"><?php esc_html_e('Add a new question', 'qnachat'); ?></p>
                    <input class="q-field" type="text">
                    <div class="actions">
                        <img class="q delete" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/cross.svg';?>">
                        <img class="q confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="q edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div> 
                <div class="answer add-new cg-hide">
                    <p class="label"><?php esc_html_e("The question's answer.", 'qnachat'); ?></p>
                    <input class="a-field" type="text">                    
                    <div class="actions">
                        <img class="a confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="a edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div>                 
            </div>
            <div class="qa cg-hide">
                <div class="question add-new">
                    <p class="label"><?php esc_html_e('Add a new question', 'qnachat'); ?></p>
                    <input class="q-field" type="text">  
                    <div class="actions">
                        <img class="q delete" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/cross.svg';?>">
                        <img class="q confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="q edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div> 
                <div class="answer add-new cg-hide">
                    <p class="label"><?php esc_html_e("The question's answer.", 'qnachat'); ?></p>
                    <input class="a-field" type="text">  
                    <div class="actions">
                        <img class="a confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="a edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div>                 
            </div>            
        </div>
        <div class="tag cg-hide">
            <div class="tag-set waiting">
                <h5><img width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/tag.png';?>"><span>Add a tag</span></h5>
                <input class="t-field" type="text">
                <div class="actions">
                    <img class="t delete" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/cross.svg';?>">
                    <img class="t edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    <img class="t confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                </div>
            </div>            
            <div class="qa waiting">
                <div class="question add-new">
                    <p class="label"><?php esc_html_e('Add a new question', 'qnachat'); ?></p>
                    <input class="q-field" type="text">
                    <div class="actions">
                        <img class="q delete" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/cross.svg';?>">
                        <img class="q confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="q edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div> 
                <div class="answer add-new cg-hide">
                    <p class="label"><?php esc_html_e("The question's answer.", 'qnachat'); ?></p>
                    <input class="a-field" type="text">                    
                    <div class="actions">
                        <img class="a confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="a edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div>                 
            </div>
            <div class="qa cg-hide">
                <div class="question add-new">
                    <p class="label"><?php esc_html_e('Add a new question', 'qnachat'); ?></p>
                    <input class="q-field" type="text">  
                    <div class="actions">
                        <img class="q delete" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/cross.svg';?>">
                        <img class="q confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="q edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div> 
                <div class="answer add-new cg-hide">
                    <p class="label"><?php esc_html_e("The question's answer.", 'qnachat'); ?></p>
                    <input class="a-field" type="text">  
                    <div class="actions">
                        <img class="a confirm" width="17" src="<?php echo esc_url(QNAC_URL) . 'assets/images/done.svg'; ?>">
                        <img class="a edit" width="17" src="<?php echo esc_url(QNAC_URL).'assets/images/edit.svg';?>">
                    </div>                     
                </div>                 
            </div>            
        </div>
    </div>
    <div>
        <a class="qnac-faq-set"><?php esc_html_e("Save Settings", 'qnachat'); ?></a>
    </div>     
</div>