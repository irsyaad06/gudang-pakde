<script setup>
import { ref } from 'vue';
import AppIcon from '@/Components/AppIcon.vue';

const copiedMermaid = ref(false);

const mermaidCode = `flowchart TB
  subgraph INTERNET["Zona Publik / Internet"]
    User["100.000 DAU Pengguna (Mobile / Desktop)"]
  end

  subgraph EDGE["Perimeter Keamanan & Edge Network"]
    CF["Cloudflare Enterprise Anycast DNS + CDN + WAF"]
  end

  subgraph AWS_VPC["Amazon Virtual Private Cloud (AWS VPC 10.0.0.0/16)"]
    subgraph PUBLIC_SUBNET["Public Ingress Subnet (Multi-AZ)"]
      IGW["Internet Gateway"]
      ALB["AWS Application Load Balancer (HTTPS / TLS 1.3)"]
      NAT["NAT Gateway (Outbound Egress Only)"]
    end

    subgraph PRIVATE_APP["Private Application Subnet (Isolated - No Public IP)"]
      ECS1["Laravel 13 ECS Fargate Task 1"]
      ECS2["Laravel 13 ECS Fargate Task 2"]
      ECSN["Laravel 13 ECS Fargate Task N (Auto-scale up to 20)"]
      WORKERS["Queue Workers Cluster (Async Thumbnail Resize)"]
    end

    subgraph PRIVATE_DATA["Private Data Subnet (Strict Isolation)"]
      RDS_PRIMARY[("AWS RDS MySQL 8.x Primary Multi-AZ")]
      RDS_REPLICA[("AWS RDS MySQL Read Replica (Read Scaling)")]
      REDIS_CLUSTER[("AWS ElastiCache Redis Cluster (Multi-Node)")]
    end
  end

  subgraph STORAGE_OPS["Storage Terkelola & Observabilitas"]
    S3[("AWS S3 Private Bucket (Product Media)")]
    MON["Datadog APM & AWS CloudWatch (Logs & Tracing)"]
    BACKUP["Automated Snapshots + Point-In-Time Recovery"]
  end

  User -->|"HTTPS (Port 443)"| CF
  CF -->|"Proxied Traffic"| ALB
  ALB -->|"Round Robin (Port 80/443)"| ECS1
  ALB -->|"Round Robin"| ECS2
  ALB -->|"Round Robin"| ECSN
  
  ECS1 -->|"Writes / Transactions"| RDS_PRIMARY
  ECS2 -->|"Writes / Transactions"| RDS_PRIMARY
  ECSN -->|"Writes / Transactions"| RDS_PRIMARY
  
  ECS1 -->|"Catalog Reads"| RDS_REPLICA
  ECS2 -->|"Catalog Reads"| RDS_REPLICA
  ECSN -->|"Catalog Reads"| RDS_REPLICA
  
  RDS_PRIMARY -.->|"Sync/Async Replication"| RDS_REPLICA
  RDS_PRIMARY -->|"Daily Automated Snapshot"| BACKUP
  
  ECS1 -->|"Session & Cache"| REDIS_CLUSTER
  ECS2 -->|"Session & Cache"| REDIS_CLUSTER
  ECSN -->|"Queue Push"| REDIS_CLUSTER
  REDIS_CLUSTER -->|"Queue Pop"| WORKERS
  
  ECS1 -->|"Pre-signed Upload URL"| S3
  WORKERS -->|"Process & Optimize"| S3
  
  ECS1 -.->|"Metrics & Logs"| MON
  ECS2 -.->|"Metrics & Logs"| MON
  ECSN -.->|"Metrics & Logs"| MON
  WORKERS -.->|"Metrics & Logs"| MON`;

const copyMermaid = () => {
    navigator.clipboard.writeText(mermaidCode).then(() => {
        copiedMermaid.value = true;
        setTimeout(() => {
            copiedMermaid.value = false;
        }, 2200);
    });
};

const activeTab = ref('visual'); // 'visual' | 'mermaid'
</script>

<template>
    <div class="rounded-2xl border border-neutral-200/90 bg-white overflow-hidden shadow-xs">
        <!-- Diagram Top Header & Switcher -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-neutral-100 bg-neutral-50/80 px-4 py-3 sm:px-5">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <AppIcon name="server" class="h-4 w-4" />
                </span>
                <div>
                    <h4 class="text-xs font-bold text-ink-900 sm:text-sm">Diagram Infrastruktur Cloud (100.000 DAU Production Design)</h4>
                    <p class="text-[11px] text-ink-500">Topologi Cloud Terisolasi: Edge CDN/WAF, ALB, Private VPC ECS, RDS Multi-AZ, S3 & Datadog</p>
                </div>
            </div>

            <!-- Tabs: Visual vs Mermaid -->
            <div class="flex items-center gap-1.5">
                <div class="inline-flex rounded-xl border border-neutral-200 bg-white p-1 text-xs">
                    <button
                        type="button"
                        class="rounded-lg px-2.5 py-1 font-semibold transition"
                        :class="activeTab === 'visual' ? 'bg-ink-900 text-white shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                        @click="activeTab = 'visual'"
                    >
                        Tampilan Visual
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-2.5 py-1 font-semibold transition"
                        :class="activeTab === 'mermaid' ? 'bg-ink-900 text-white shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                        @click="activeTab = 'mermaid'"
                    >
                        Kode Mermaid
                    </button>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-neutral-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-ink-700 transition hover:bg-neutral-50"
                    @click="copyMermaid"
                    :title="copiedMermaid ? 'Tersalin ke clipboard!' : 'Salin kode Mermaid'"
                >
                    <AppIcon :name="copiedMermaid ? 'check' : 'copy'" class="h-3.5 w-3.5" :class="{ 'text-emerald-600': copiedMermaid }" />
                    <span class="hidden sm:inline">{{ copiedMermaid ? 'Tersalin!' : 'Salin Mermaid' }}</span>
                </button>
            </div>
        </div>

        <!-- Tab 1: Visual Interactive Diagram -->
        <div v-show="activeTab === 'visual'" class="p-4 sm:p-6 bg-gradient-to-b from-neutral-50/50 to-white">
            <div class="space-y-4">
                <!-- Zone 1: Users & Edge -->
                <div class="rounded-2xl border-2 border-orange-200/90 bg-orange-50/30 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-orange-600 text-[11px] font-bold text-white">1</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-orange-950">Zona Edge & Perlindungan DDoS</span>
                        </div>
                        <span class="rounded-md bg-orange-100 px-2 py-0.5 font-mono text-[10px] font-bold text-orange-800">Cloudflare Edge (300+ PoP)</span>
                    </div>
                    <div class="mt-3 grid grid-cols-1 gap-2.5 sm:grid-cols-3 text-xs">
                        <div class="rounded-xl border border-orange-200 bg-white p-3">
                            <div class="font-bold text-ink-900">Anycast DNS & SSL</div>
                            <div class="text-[11px] text-ink-600 mt-0.5">TLS 1.3 handshake terdekat dengan pengguna; eliminasi latensi geografis di Indonesia.</div>
                        </div>
                        <div class="rounded-xl border border-orange-200 bg-white p-3">
                            <div class="font-bold text-ink-900">Cloudflare WAF & Bot Shield</div>
                            <div class="text-[11px] text-ink-600 mt-0.5">Blokir otomatis injeksi SQL, scraping brutal katalog, dan serangan L3/L4/L7 sebelum mencapai server.</div>
                        </div>
                        <div class="rounded-xl border border-orange-200 bg-white p-3">
                            <div class="font-bold text-ink-900">Static Asset Edge Cache</div>
                            <div class="text-[11px] text-ink-600 mt-0.5">80%+ hit ratio untuk gambar WebP dan bundle JS Vite. Menghemat ~70% bandwidth origin server.</div>
                        </div>
                    </div>
                </div>

                <!-- Arrow down -->
                <div class="flex justify-center">
                    <div class="flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-3 py-1 font-mono text-[10px] text-ink-500 shadow-xs">
                        <span>HTTPS ke Ingress AWS ALB (Dual-AZ)</span>
                        <AppIcon name="chevron-down" class="h-3 w-3 text-ink-400" />
                    </div>
                </div>

                <!-- Zone 2: AWS VPC Private Subnets -->
                <div class="rounded-2xl border-2 border-blue-200/90 bg-blue-50/20 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-blue-600 text-[11px] font-bold text-white">2</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-950">Amazon Virtual Private Cloud (AWS VPC)</span>
                        </div>
                        <span class="rounded-md bg-blue-100 px-2 py-0.5 font-mono text-[10px] font-bold text-blue-800">10.0.0.0/16 Network</span>
                    </div>

                    <div class="mt-4 space-y-3">
                        <!-- Public Ingress Subnet -->
                        <div class="rounded-xl border border-blue-200 bg-white p-3.5">
                            <div class="flex items-center justify-between text-xs font-bold text-blue-900">
                                <span>A. Public Ingress Subnet (Multi-AZ)</span>
                                <span class="font-mono text-[10px] text-ink-500">Port 80/443 Only</span>
                            </div>
                            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2 text-xs">
                                <div class="rounded-lg bg-blue-50/60 p-2.5 border border-blue-100">
                                    <div class="font-bold text-ink-900">AWS Application Load Balancer (ALB)</div>
                                    <div class="text-[11px] text-ink-600">Health checks otomatis <code>/up</code> (interval 10s). Mengarahkan traffic seimbang ke cluster ECS.</div>
                                </div>
                                <div class="rounded-lg bg-blue-50/60 p-2.5 border border-blue-100">
                                    <div class="font-bold text-ink-900">NAT Gateway</div>
                                    <div class="text-[11px] text-ink-600">Akses keluar terisolasi untuk update package tanpa mengekspos IP publik ke instance backend.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Private App Compute Subnet -->
                        <div class="rounded-xl border border-indigo-200 bg-white p-3.5">
                            <div class="flex items-center justify-between text-xs font-bold text-indigo-900">
                                <span>B. Private Application Subnet (Strict Isolation — Zero Public IP)</span>
                                <span class="rounded-md bg-indigo-50 px-2 py-0.5 font-mono text-[10px] text-indigo-700">Auto-scale 3 s/d 20 Tasks</span>
                            </div>
                            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2 text-xs">
                                <div class="rounded-lg bg-indigo-50/60 p-2.5 border border-indigo-100">
                                    <div class="font-bold text-ink-900">AWS ECS Fargate Cluster (Web App)</div>
                                    <div class="text-[11px] text-ink-600">Serverless container Laravel 13 (PHP 8.4-FPM + Nginx). Auto-scale bertambah saat CPU > 65% atau latency > 250ms.</div>
                                </div>
                                <div class="rounded-lg bg-indigo-50/60 p-2.5 border border-indigo-100">
                                    <div class="font-bold text-ink-900">AWS ECS Fargate Workers (Background Jobs)</div>
                                    <div class="text-[11px] text-ink-600">Worker khusus pemrosesan gambar foto barang, resize thumbnail async, dan pembersihan media kadaluarsa.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Private Data Subnet -->
                        <div class="rounded-xl border border-emerald-200 bg-white p-3.5">
                            <div class="flex items-center justify-between text-xs font-bold text-emerald-900">
                                <span>C. Private Database & In-Memory Subnet (Tertutup Total dari Publik)</span>
                                <span class="rounded-md bg-emerald-50 px-2 py-0.5 font-mono text-[10px] text-emerald-700">Security Group Rule: Port 3306 & 6379 Hanya dari ECS</span>
                            </div>
                            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-3 text-xs">
                                <div class="rounded-lg bg-emerald-50/60 p-2.5 border border-emerald-100">
                                    <div class="font-bold text-ink-900">AWS RDS MySQL Primary</div>
                                    <div class="text-[11px] text-ink-600">Multi-AZ synchronous failover untuk menulis data barang, akun & transaksi.</div>
                                </div>
                                <div class="rounded-lg bg-emerald-50/60 p-2.5 border border-emerald-100">
                                    <div class="font-bold text-ink-900">AWS RDS Read Replica</div>
                                    <div class="text-[11px] text-ink-600">Khusus query pencarian & filter katalog berulang (menghandle 90% traffic 100k DAU).</div>
                                </div>
                                <div class="rounded-lg bg-emerald-50/60 p-2.5 border border-emerald-100">
                                    <div class="font-bold text-ink-900">AWS ElastiCache Redis</div>
                                    <div class="text-[11px] text-ink-600">Cluster session store & cache tags terdistribusi dengan in-memory latensi &lt; 2ms.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zone 3: Storage, Observability & Recovery -->
                <div class="rounded-2xl border-2 border-purple-200/90 bg-purple-50/30 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-purple-600 text-[11px] font-bold text-white">3</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-purple-950">Storage Terisolasi, Pemulihan Bencana & Monitoring</span>
                        </div>
                        <span class="rounded-md bg-purple-100 px-2 py-0.5 font-mono text-[10px] font-bold text-purple-800">AWS S3 + Datadog + PITR</span>
                    </div>
                    <div class="mt-3 grid grid-cols-1 gap-2.5 sm:grid-cols-3 text-xs">
                        <div class="rounded-xl border border-purple-200 bg-white p-3">
                            <div class="font-bold text-ink-900">AWS S3 / Cloudflare R2 Media</div>
                            <div class="text-[11px] text-ink-600 mt-0.5">Bucket privat dengan pre-signed URL; tidak ada foto yang disimpan di disk ephemeral container.</div>
                        </div>
                        <div class="rounded-xl border border-purple-200 bg-white p-3">
                            <div class="font-bold text-ink-900">Datadog APM & CloudWatch</div>
                            <div class="text-[11px] text-ink-600 mt-0.5">Monitoring RPS real-time, query lambat (>100ms), 5xx error rate, dan health alerts via Slack/Email.</div>
                        </div>
                        <div class="rounded-xl border border-purple-200 bg-white p-3">
                            <div class="font-bold text-ink-900">Backup & Point-In-Time Recovery</div>
                            <div class="text-[11px] text-ink-600 mt-0.5">Snapshot otomatis tiap 24 jam + continuous WAL binlog archiving. <strong>RPO &lt; 5 menit, RTO &lt; 30 menit</strong>.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Raw Mermaid Code -->
        <div v-show="activeTab === 'mermaid'" class="p-4 sm:p-5 bg-neutral-900 text-neutral-200">
            <div class="flex items-center justify-between pb-3 border-b border-neutral-800">
                <span class="font-mono text-xs text-neutral-400">mermaid: infrastructure-architecture.mmd</span>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 text-xs font-mono text-emerald-400 hover:text-emerald-300"
                    @click="copyMermaid"
                >
                    <AppIcon :name="copiedMermaid ? 'check' : 'copy'" class="h-3.5 w-3.5" />
                    <span>{{ copiedMermaid ? 'Tersalin!' : 'Salin Kode' }}</span>
                </button>
            </div>
            <pre class="mt-3 overflow-x-auto font-mono text-xs leading-relaxed text-emerald-300"><code>{{ mermaidCode }}</code></pre>
        </div>
    </div>
</template>
