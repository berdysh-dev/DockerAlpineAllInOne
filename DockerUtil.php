<?php

    class DockerUtil{

        const
            REPOSITORY  = 'REPOSITORY'  ,
            TAG         = 'TAG'         ,
            ContainerID = 'ContainerID' ,
            ImageID     = 'ImageID'     ;

        function __construct(){
            $this->UsePodman = FALSE ;
        }

        function sh($command){

            $lines = [] ;

            $fifo = ['','',''] ;

            $descriptor_spec = [
                ['pipe', 'r'],
                ['pipe', 'w'],
                ['pipe', 'w'],
            ] ;

            $pipes      = null ;
            $cwd        = null ;
            $env_vars   = null ;
            $options    = null ;

            $process = proc_open(
                $command        ,
                $descriptor_spec,
                $pipes          ,
                $cwd            ,
                $env_vars       ,
                $options
            ) ;

            if(is_resource($process)){
                stream_set_blocking($pipes[1],0) ; 
                stream_set_blocking($pipes[2],0) ; 

                for(;;){
                    if(feof($pipes[1]) || feof($pipes[2])){ break ; }

                    $rd_fds = [$pipes[1],$pipes[2]] ;
                    $wr_fds = null ;
                    $ex_fds = null ;
                    $timeout = 1 ;

                    $cnt = stream_select($rd_fds,$wr_fds,$ex_fds,$timeout) ;
                    if($cnt === FALSE){
                        echo "err.\n" ;
                        break ;
                    }else if($cnt === 0){
                        continue ;
                    }else if($cnt > 0){
                        if(is_array($rd_fds)){
                            foreach($rd_fds as $fd){
                                if($fd === $pipes[1]){ $kind = 1 ; }
                                if($fd === $pipes[2]){ $kind = 2 ; }
                                if(($inp = stream_get_contents($fd)) !== FALSE){
                                    $fifo[$kind] .= $inp ;
                                    for(;;){
                                        if(($pos = strpos($fifo[$kind],"\n")) === FALSE){
                                            break ;
                                        }else{
                                            $line = substr($fifo[$kind],0,$pos) ;
                                            $lines[] = $line ;
                                            $fifo[$kind] = substr($fifo[$kind],$pos+1) ;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $stat = proc_close($process) ;

                return [$stat,$lines] ;
            }

            return [FALSE,FALSE] ;
        }

        function rmi(){
            $items = $this->DockerImages() ;
            foreach($items as $item){
                if($this->UsePodman){
                    $cmd = sprintf("podman rmi -f %s",$item[self::ImageID]) ;
                }else{
                    $cmd = sprintf("docker rmi -f %s",$item[self::ImageID]) ;
                }

                echo $cmd . "\n" ;
                list($stat,$lines) = $this->sh($cmd) ;
                print_r($lines) ;
            }
        }

        function DockerImages(){
            $ret = [] ;

            if($this->UsePodman){
                list($stat,$lines) = $this->sh('podman images') ;
            }else{
                list($stat,$lines) = $this->sh('docker images') ;
            }

            if(($stat == 0) && is_array($lines)){
                foreach($lines as $line){
                    if(preg_match('/^([^\s]+)\s+([^\s]+)\s+([a-z0-9]+)\s+(.*)$/',$line,$matches) === 1){
                        $ret[] = [
                            self::REPOSITORY => $matches[1],
                            self::TAG       => $matches[2],
                            self::ImageID   => $matches[3]
                        ] ;
                    }else{
                        // printf("BAD[%s]\n",$line) ;
                    }
                }
            }
            return $ret ;
        }

        function ImageID($tag = FALSE){
            $items = $this->DockerImages() ;

            foreach($items as $item){
                if($item[self::TAG] === $tag){
                    echo $item[self::ImageID] ;
                    exit ;
                }
            }
        }

        function Ps_A($tag=FALSE){
            $ret = [] ;

            if($this->UsePodman){
                list($stat,$lines) = $this->sh('podman ps -a') ;
            }else{
                list($stat,$lines) = $this->sh('docker ps -a') ;
            }

            foreach($lines as $line){
                if(preg_match('/^([^\s]+)\s+([^\s]+)\s+\"([^\"]+)\"\s+(.*)$/',$line,$matches) === 1){
                    $ContainerID = $matches[1] ;
                    $ImageID = $matches[2] ;
                    $ret[] = [
                        self::ContainerID => $ContainerID,
                        self::ImageID => $ImageID
                    ] ;
                }else if(preg_match('/^([a-f0-9]+)\s+([^\s]+)\s+(.*)$/',$line,$matches) === 1){
                    $ContainerID = $matches[1] ;

                    $ret[] = [
                        self::ContainerID => $ContainerID
                    ] ;
                }else{
                    // printf("BAD[%s]\n",$line) ;
                }
            }
            return $ret ;
        }

        function DockerRm($ContainerID){
            $flag = FALSE ;

            if($this->UsePodman){
                $cmd = sprintf('podman rm %s',$ContainerID) ;
            }else{
                $cmd = sprintf('docker rm %s',$ContainerID) ;
            }

            echo $cmd . "\n" ;
            list($stat,$lines) = $this->sh($cmd) ;

            print_r($lines) ;

            foreach($lines as $line){
                if($line === $ContainerID){
                    $flag = TRUE ;
                }
            }
            return (($stat === 0) && ($flag === TRUE)) ? TRUE : FALSE ;
        }

        function DockerStop($ContainerID){
            $flag = FALSE ;

            if($this->UsePodman){
                $cmd = sprintf('podman stop %s',$ContainerID) ;
            }else{
                $cmd = sprintf('docker stop %s',$ContainerID) ;
            }

            echo $cmd . "\n" ;
            list($stat,$lines) = $this->sh($cmd) ;

            print_r($lines) ;

            foreach($lines as $line){
                if($line === $ContainerID){
                    $flag = TRUE ;
                }
            }
            return (($stat === 0) && ($flag === TRUE)) ? TRUE : FALSE ;
        }

        function Kill($tag=FALSE){
            $procs = $this->Ps_A($tag) ;

            if(is_array($procs)){
                foreach($procs as $proc){
                    if($this->DockerStop($proc[self::ContainerID]) !== TRUE){
                        echo "失敗\n" ;
                    }else{
                        if($this->DockerRm($proc[self::ContainerID]) !== TRUE){
                            echo "失敗\n" ;
                        }else{
                            echo "成功\n" ;
                        }
                    }
                }
            }
        }

        function ContainerID($tag=FALSE){
            $procs = $this->Ps_A($tag) ;
            if(is_array($procs)){
                foreach($procs as $proc){
                    echo $proc[self::ContainerID] ;
                    exit ;
                }
            }
        }
    }

    if($dockerUtil = new DockerUtil()){
        if(count($argv) > 1){
            if(count($argv) > 2){
                $tag = $argv[2] ;
            }else{
                $tag = FALSE ;
            }
            switch($opt = $argv[1]){
            case '-rmi':
                $dockerUtil->rmi() ;
                break ;
            case '-ImageID':
                $dockerUtil->ImageID($tag) ;
                break ;
            case '-ContainerID':
                $dockerUtil->ContainerID($tag) ;
                break ;
            case '-kill':
                $dockerUtil->Kill($tag) ;
                break ;
            case '-Ps_A':
                $dockerUtil->Ps_A($tag) ;
                break ;
            }
        }
    }
