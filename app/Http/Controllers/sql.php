SELECT  T.agent_id,
                                T.agent_name,
                                SUM(T.leads_count) AS leads_count,
                                SUM(T.leads_reassigned) AS leads_reassigned,
                                SUM(T.leads_rejected) AS leads_rejected,
                                -- To include requested leads
                                SUM(T.requested_leads) AS requested_leads,
                                MAX(T.last_lead) AS last_lead
                        FROM (
                            SELECT  LM.agent_id as agent_id,
                                    U.name AS agent_name,
                                    COUNT(
                                        CASE
                                            WHEN LM.rejected <> 1 THEN
                                                1
                                            ELSE
                                                0
                                        END
                                    ) AS leads_count,
                                    0 AS leads_reassigned,
                                    SUM(LM.rejected) AS leads_rejected,
                                    -- New line added here to calculate requested leads by an agent
                                    SUM(CASE WHEN LM.old_agent_id IS NULL THEN 1 ELSE 0 END) AS requested_leads,
                                    MAX(CONVERT_TZ(LM.updated_at, '+00:00', '-05:00')) AS last_lead
                            FROM lead_mails LM
                            INNER JOIN users U ON U.id = LM.agent_id
                            WHERE   LM.updated_at >= '2024-05-03 00:00:00' AND
                                    LM.updated_at <= '2024-05-03 23:59:59'
                            GROUP BY LM.agent_id, U.name
                        
                            UNION ALL
                        
                            SELECT 
                                    U.id as agent_id,
                                    U.name AS agent_name,
                                    SUM(CASE WHEN IFNULL(LM.old_agent_id, 0) > 0 THEN 1 ELSE 0 END) AS leads_count,
                                    SUM(CASE WHEN IFNULL(LM.old_agent_id, 0) > 0 THEN 1 ELSE 0 END) AS leads_reassigned,
                                    0 AS leads_rejected, -- Dummy for alignment
                                    0 AS requested_leads, -- Ensure this column is included in all parts
                                    MAX(CONVERT_TZ(LM.updated_at, '+00:00', '-05:00')) AS last_lead
                            FROM lead_mails LM        
                            INNER JOIN users U ON U.id = LM.old_agent_id
                            WHERE   LM.updated_at >= '2024-05-03 00:00:00' AND
                                    LM.updated_at <= '2024-05-03 23:59:59'
                            GROUP BY U.id, U.name

                            UNION ALL

                            SELECT 
                                    U.id as agent_id,
                                    U.name AS agent_name,
                                    0 AS leads_count,
                                    0 AS leads_reassigned,
                                    0 AS leads_rejected, -- Dummy for alignment
                                    SUM(CASE WHEN IFNULL(LM.old_agent_id, 0) > 0 THEN 1 ELSE 0 END) AS requested_leads, -- Ensure this column is included in all parts
                                    MAX(CONVERT_TZ(LM.updated_at, '+00:00', '-05:00')) AS last_lead
                            FROM lead_mails LM        
                                INNER JOIN users U ON
                                    U.id = LM.agent_id
                            WHERE   LM.updated_at >= '2024-05-03 00:00:00' AND
                                    LM.updated_at <= '2024-05-03 23:59:59'
                            GROUP BY U.id, U.name

                            UNION ALL

                            SELECT LM.agent_id,
                                'Not Assigned' AS agent_name,
                                COUNT(*) AS leads_count,
                                0 AS leads_reassigned,
                                SUM(LM.rejected) AS leads_rejected,
                                0 AS requested_leads, -- Added to match the column count and order
                                MAX(CONVERT_TZ(LM.updated_at, '+00:00', '-05:00')) AS last_lead
                            FROM lead_mails LM
                            WHERE   LM.updated_at >= '2024-05-03 00:00:00' AND
                                    LM.updated_at <= '2024-05-03 23:59:59' AND
                                    LM.agent_id = 0
                            GROUP BY LM.agent_id
                            ) T
                        GROUP BY T.agent_id, T.agent_name
                        ORDER BY agent_name