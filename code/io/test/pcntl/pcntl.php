<?php
/**
 * 1.从执行的多次结果得知，程序从外到内创建fork。然后再从内最后一次fork开始退出
 * 2.如一次fork之后，程序的父进程因pcntl_wait阻塞，然后等待本次fork的子进程退出，然后相应的子进程的父进程执行逻辑并退出
 * 3.然后执行本子进程的父进程依次循环2的逻辑退出，最终结束总进程
 */
if (strtolower(php_sapi_name()) != 'cli') {
    die("请在cli模式下运行");
}
$index = 0;
$loop  = 2;
while ($index < $loop) {
    echo "当前进程：" . posix_getpid() . PHP_EOL;
    $pid = pcntl_fork(); //fork出子进程
    if ($pid == -1) { // 创建错误，返回-1
        die('进程fork失败');
    } else if ($pid) {               // $pid > 0, 如果fork成功，返回子进程id
        // 父进程逻辑
        echo "进入父进程的代码段".PHP_EOL;
//        pcntl_wait($status);         // 父进程必须等待一个子进程退出后，再创建下一个子进程。
        $child_id = $pid;            //子进程的ID
        $pid      = posix_getpid();  //获取当前进程Id
        $ppid     = posix_getppid(); // 进程的父级ID
        $time     = microtime(true);
        echo "我是父进程，当前进程id:{$pid}；fork的子进程id: {$child_id}；父进程id:{$ppid}; 当前index:{$index}; 当前时间:{$time}" . PHP_EOL;
    } else { // $pid = 0
        // 子进程逻辑
        echo "进入子进程的代码段".PHP_EOL;
        $cid  = $pid;
        $pid  = posix_getpid();
        $ppid = posix_getppid();
        $time = microtime(true);
        echo "我是子进程，当前进程id:{$pid}；父进程id:{$ppid}; 当前index:{$index}; 当前时间:{$time}" . PHP_EOL;
        exit;
    }
    $index++;
}

while (true) {
    sleep(1);
}