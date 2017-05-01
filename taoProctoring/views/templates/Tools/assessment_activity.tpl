<?php

$assessmentActivityConfig   = get_data('assessment_activity_config');
$completedAssessmentsConfig = get_data('completed_assessments_config');
$data                       = get_data('activity_data');
$reasonCategories           = get_data('reasonCategories');

?>

<div class="activity-dashboard" data-config="<?= _dh(json_encode($assessmentActivityConfig)) ?>">
    <div class="grid-row">
        <div class="col-9">
            <div class="grid-row">
               <h2><?= __('User Activity') ?></h2>
            </div>
            <div class="grid-row user-activity">
                <div class="col-6 dashboard-block">
                    <span class="dashboard-block-number active-test-takers"><?= $data['active_test_takers'] ?></span>
                    <h3><span class="icon icon-test-takers"></span> <?= __('Active test-takers') ?></h3>
                </div>
                <div class="col-6 dashboard-block">
                    <span class="dashboard-block-number active-proctors"><?= $data['active_proctors'] ?></span>
                    <h3><span class="icon icon-test-taker"></span> <?= __('Active proctors') ?></h3>
                </div>
            </div>
            <div class="grid-row">
                <h2><?= __('Current Assessment Activity') ?></h2>
            </div>
            <div class="grid-row assessment-activity">
                <div class="col-4 dashboard-block">
                    <h4><span class="icon icon-play"></span> <?= __('Total Current Assessments') ?></h4>
                    <span class="dashboard-block-number total-current-assessments"><?= $data['total_current_assessments'] ?></span>
                </div>
                <div class="col-2 dashboard-block">
                    <h4><span class="icon icon-play"></span> <?= __('In Progress') ?></h4>
                    <span class="dashboard-block-number in-progress-assessments"><?= $data['in_progress_assessments'] ?></span>
                </div>
                <div class="col-2 dashboard-block">
                    <h4><span class="icon icon-time"></span> <?= __('Awaiting') ?></h4>
                    <span class="dashboard-block-number awaiting-assessments"><?= $data['awaiting_assessments'] ?></span>
                </div>
                <div class="col-2 dashboard-block">
                    <h4><span class="icon icon-continue"></span> <?= __('Authorized') ?></h4>
                    <span class="dashboard-block-number authorized-but-not-started-assessments"><?= $data['authorized_but_not_started_assessments'] ?></span>
                </div>
                <div class="col-2 dashboard-block">
                    <h4><span class="icon icon-pause"></span> <?= __('Paused') ?></h4>
                    <span class="dashboard-block-number paused-assessments"><?= $data['paused_assessments'] ?></span>
                </div>
            </div>
            <div class="grid-row">
                <div class="col-12 activity-chart">
                    <select class="select2 js-activity-chart-interval" data-has-search="false">
                        <option value="day"><?= __('Last Day') ?></option>
                        <option value="week"><?= __('Last Week') ?></option>
                        <option value="month"><?= __('Last Month') ?></option>
                        <option value="prevmonth"><?= __('Previous Month') ?></option>
                    </select>
                    <div class="js-completed-assessments" data-config="<?= _dh(json_encode($completedAssessmentsConfig)) ?>"></div>
                </div>
            </div>
            <div class="grid-row">
                <div class="col-12">
                    <div class="js-delivery-list"></div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="grid-row">
                <h2><?= __('Actions') ?></h2>
            </div>
            <div class="js-pause-active-executions-container" data-reasonCategories="<?= _dh(json_encode($reasonCategories)) ?>">
                <button class="js-pause btn-warning" type="button"><?= __('Pause Active Test Sessions') ?></button>
            </div>
        </div>
    </div>
</div>