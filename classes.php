<?php

class WP_Customize_Control_Group extends WP_Customize_Control
{
    public $type = 'select';

    public function render_content()
    {
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <select <?php $this->link(); ?>>
                <option <?php if ($this->value() == 'gbg') echo "Selected"; ?> value="gbg">Bussines</option>
                <option <?php if ($this->value() == 'gdg') echo "Selected"; ?> value="gdg">Developers</option>
            </select>
        </label>
    <?php
    }
}